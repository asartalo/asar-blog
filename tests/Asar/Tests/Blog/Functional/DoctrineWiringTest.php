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

    protected function createBasicBlog()
    {
        return $this->manager->newBlog('FooBlog', array('description' => 'The foo blog'));
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
        $blog = $this->createBasicBlog();
        $this->assertEquals('FooBlog', $blog->getName());
        $this->assertEquals('The foo blog', $blog->getDescription());
    }

    /**
     * Test commits changes to blog
     */
    public function testCommitsChanges()
    {
        $blog = $this->createBasicBlog();
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
        $blog = $this->createBasicBlog();
        $this->manager->commit();
        //$this->manager = Manager::createManager();
        $blog = $this->manager->getBlog(1);
        $this->assertEquals('FooBlog', $blog->getName());
        $this->assertEquals('The foo blog', $blog->getDescription());
    }

    private function createTestAuthor()
    {
        return $this->manager->newAuthor('Pedro', 'pedro@example.com');
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
        $blog = $this->createBasicBlog();
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
        $this->createBasicBlog();
        $author = $this->createTestAuthor();
        $this->manager->commit();
        $post = $this->writeAPost($author);
        $this->assertEquals('My first Post', $post->getTitle());
        $this->assertEquals($this->manager->getCurrentBlog(), $post->getBlog());
    }

    /**
     * Retrieving a post
     */
    public function testRetrievingAPost()
    {
        $this->createBasicBlog();
        $author = $this->createTestAuthor();
        $this->manager->commit();
        $this->writeAPost($author);
        $this->manager->commit();
        $post = $this->manager->getPost(1);
        $this->assertEquals('My first Post', $post->getTitle());
        $this->assertEquals($this->manager->getCurrentBlog(), $post->getBlog());
    }

    /**
     * Creating a category
     */
    public function testCreatingACategory()
    {
        $this->createBasicBlog();
        $this->manager->commit();
        $this->manager->manage('FooBlog');
        $category = $this->manager->newCategory('foo');
        $this->assertEquals('foo', $category->getName());
    }

    /**
     * Adding a post to a category
     */
    public function testCategorizingAPost()
    {
        $this->createBasicBlog();
        $this->createTestAuthor();
        $this->manager->commit();
        $this->manager->manage('FooBlog');
        $this->manager->newCategory('fooCategory');
        $this->manager->commit();
        $author = $this->manager->getAuthor('Pedro');
        $post1 = $this->writeAPost($author);
        $post2 = $this->writeAPost($author, "Woo");
        $this->manager->addToCategory('fooCategory', $post2);
        $this->manager->commit();
        $posts = $this->manager->getPostsInCategory('fooCategory');
        $this->assertEquals(1, count($posts));
        $this->assertEquals($post2, $posts[0]);
    }

    /**
     * Retrieving all posts
     */
    public function testGetAllPosts()
    {
        $this->createBasicBlog();
        $this->createTestAuthor();
        $this->manager->commit();
        $this->manager->manage('FooBlog');
        $this->manager->newCategory('fooCategory');
        $this->manager->commit();
        $author = $this->manager->getAuthor('Pedro');
        $post1 = $this->writeAPost($author);
        $post2 = $this->writeAPost($author, "Woo");
        $this->manager->addToCategory('fooCategory', $post2);
        $this->manager->commit();

        $posts = $this->manager->getPosts();
        $this->assertEquals(2, count($posts));
        $this->assertEquals($post1, $posts[0]);
        $this->assertEquals($post2, $posts[1]);
    }

}