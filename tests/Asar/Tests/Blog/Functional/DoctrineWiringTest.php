<?php
/**
 * This file is part of the Asar Blog library
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\Tests\Blog\Functional;

use Asar\TestHelper\TestCase;
use Asar\Blog\Manager;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Some basic tests for testing doctrine wiring
 */
class DoctrineWiringTest extends TestCase
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->manager = Manager::createManagerForTest();

        // Retrieve the Doctrine 2 entity manager
        $this->em = $this->manager->getEntityManager();

        // Instantiate the schema tool
        $tool = new SchemaTool($this->em);

        // Retrieve all of the mapping metadata
        $classes = $this->em->getMetadataFactory()->getAllMetadata();

        // Delete the existing test database schema
        $tool->dropSchema($classes);

        // Create the test database schema
        $tool->createSchema($classes);
    }

    protected function contextBasicBlog()
    {
        $this->blog = $this->manager->newBlog('FooBlog', array('description' => 'The foo blog'));

        return $this->blog;
    }

    private function createTestAuthor()
    {
        return $this->manager->newAuthor('Pedro', 'pedro@example.com');
    }

    private function writeAPost($author, $title = 'My first Post')
    {
        $this->manager->manage('FooBlog');

        return $this->manager->newPost(
            $author,
            array(
                'title' => $title,
                'summary' => 'Hello',
                'content' => 'Hello world!',
            )
        );
    }

    /**
     * Test creating a blog
     */
    public function testCreatingABlog()
    {
        $blog = $this->contextBasicBlog();
        $this->assertEquals('FooBlog', $blog->getName());
        $this->assertEquals('The foo blog', $blog->getDescription());
    }

    /**
     * Test commits changes to blog
     */
    public function testCommitsChanges()
    {
        $blog = $this->contextBasicBlog();
        $this->manager->commit();
        $blog = $this->manager->getBlog('FooBlog');
        $this->assertEquals('FooBlog', $blog->getName());
        $this->assertEquals('The foo blog', $blog->getDescription());
    }

    /**
     * Test getting a blog by id
     */
    public function testGetABlogById()
    {
        $blog = $this->contextBasicBlog();
        $this->manager->commit();
        $blog = $this->manager->getBlog(1);
        $this->assertEquals('FooBlog', $blog->getName());
        $this->assertEquals('The foo blog', $blog->getDescription());
    }

    /**
     * A new author
     */
    public function testNewAuthor()
    {
        $author = $this->createTestAuthor();
        $this->assertInstanceOf('Asar\Blog\Author', $author);
        $this->assertEquals('Pedro', $author->getName());
    }

    /**
     * Getting an author by name
     */
    public function testGettingAnAuthor()
    {
        $blog = $this->contextBasicBlog();
        $this->createTestAuthor();
        $this->manager->commit();
        $author = $this->manager->getAuthor('Pedro');
        $this->assertEquals('Pedro', $author->getName());
    }

    /**
     * Writing a post
     */
    public function testWritingAPost()
    {
        $this->contextBasicBlog();
        $author = $this->createTestAuthor();
        $this->manager->commit();
        $post = $this->writeAPost($author);
        $this->assertEquals('My first Post', $post->getTitle());
        $this->assertEquals($this->manager->getCurrentBlog(), $post->getBlog());
    }

    /**
     * Editing a post
     */
    public function testEditingAPost()
    {
        $this->contextBasicBlog();
        $author = $this->createTestAuthor();
        $this->manager->commit();
        $post = $this->writeAPost($author);
        $this->manager->commit();
        $id = $post->getId();
        $this->manager->editPost(
            $post,
            array(
                'title' => 'Yellow',
                'summary' => 'Yellow',
                'content' => 'Yellow world!',
            )
        );
        $this->manager->commit();
        $post = $this->manager->getPost($id);
        $this->assertEquals('Yellow', $post->getTitle());
    }

    /**
     * Retrieving a post
     */
    public function testRetrievingAPost()
    {
        $this->contextBasicBlog();
        $author = $this->createTestAuthor();
        $this->manager->commit();
        $this->writeAPost($author);
        $this->manager->commit();
        $post = $this->manager->getPost(1);
        $this->assertEquals('My first Post', $post->getTitle());
        $this->assertEquals($this->manager->getCurrentBlog(), $post->getBlog());
    }

    /**
     * Retrieving a non-existent post
     */
    public function testRetrievingAnUknownPost()
    {
        $this->contextBasicBlog();
        $this->manager->commit();
        $this->manager->manage('FooBlog');
        $this->assertTrue($this->manager->getPost(1)->isNull());
    }

    /**
     * Retrieving a post via slug
     */
    public function testRetrievingAPostViaSlug()
    {
        $this->contextBasicBlog();
        $author = $this->createTestAuthor();
        $this->manager->commit();
        $this->writeAPost($author);
        $this->manager->commit();
        $post = $this->manager->getPost('my-first-post');
        $this->assertEquals('My first Post', $post->getTitle());
        $this->assertEquals($this->manager->getCurrentBlog(), $post->getBlog());
    }

    /**
     * Creating a category
     */
    public function testCreatingACategory()
    {
        $this->contextBasicBlog();
        $this->manager->commit();
        $this->manager->manage('FooBlog');
        $category = $this->manager->newCategory('foo');
        $this->assertEquals('foo', $category->getName());
    }

    private function contextBlogWithCategory($category = 'fooCategory')
    {
        $this->contextBasicBlog();
        $this->createTestAuthor();
        $this->manager->commit();
        $this->manager->manage('FooBlog');
        $this->manager->newCategory($category);
        $this->manager->commit();
    }

    private function contextBlogWithTwoPostsOneInACategory()
    {
        $this->contextBlogWithCategory();
        $this->author = $this->manager->getAuthor('Pedro');
        $this->post1 = $this->writeAPost($this->author);
        $this->post2 = $this->writeAPost($this->author, "Woo");
        $this->manager->addToCategory('fooCategory', $this->post2);
        $this->manager->commit();
    }

    /**
     * Adding a post to a category
     */
    public function testCategorizingAPost()
    {
        $this->contextBlogWithTwoPostsOneInACategory();

        $posts = $this->manager->getPostsInCategory('fooCategory');
        $this->assertEquals(1, count($posts));
        $this->assertEquals($this->post2, $posts[0]);
    }

    /**
     * Retrieving all posts
     */
    public function testGetAllPosts()
    {
        $this->contextBlogWithTwoPostsOneInACategory();

        $posts = $this->manager->getPosts();
        $this->assertEquals(2, count($posts));
        $this->assertEquals($this->post1, $posts[0]);
        $this->assertEquals($this->post2, $posts[1]);
    }

    /**
     * Retrieving published posts
     */
    public function testGetAllPublishedPosts()
    {
        $this->contextBlogWithTwoPostsOneInACategory();

        $this->post1->publish();
        $this->manager->commit();

        $posts = $this->manager->getPosts(array('published' => true));
        $this->assertEquals(1, count($posts));
        $this->assertEquals($this->post1, $posts[0]);
    }

    /**
     * Posts should be sorted by date published with latest first
     */
    public function testPostsAreSorted()
    {
        $this->contextBlogWithTwoPostsOneInACategory();
        $this->post1->publish();
        sleep(1);
        $this->post2->publish();
        $this->manager->commit();

        $posts = $this->manager->getPosts(array('published' => true));
        $this->assertEquals(2, count($posts));
        $this->assertEquals($this->post2, $posts[0]);
        $this->assertEquals($this->post1, $posts[1]);
    }

    /**
     * Retrieving published posts in a category
     */
    public function testGetAllPublishedPostsInACategory()
    {
        $this->contextBlogWithTwoPostsOneInACategory();

        $post3 = $this->writeAPost($this->author, "Woo2");
        $this->manager->addToCategory('fooCategory', $post3);
        $this->post2->publish();
        $this->manager->commit();

        $posts = $this->manager->getPostsInCategory(
            'fooCategory', array('published' => true)
        );
        $this->assertEquals(1, count($posts));
        $this->assertEquals($this->post2, $posts[0]);
    }

    /**
     * Paginating through posts
     */
    public function testPostPagination()
    {
        $this->contextBlogWithTwoPostsOneInACategory();

        for ($i=0; $i < 9; $i++) {
            $this->writeAPost($this->author, "Woo $i");
        }
        $this->manager->commit();

        $posts = $this->manager->getPosts(array('paginateBy' => 3))->page(1);
        $i = 0;
        foreach ($posts as $post) {
            $i++;
        }
        $this->assertEquals(3, $i);
    }

}