<?php
/**
 * This file is part of the Asar Blog library
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\Tests\Blog\Unit;

use Asar\TestHelper\TestCase;
use Asar\Blog\Post;
use Asar\Blog\Blog;
use Asar\Blog\Author;

/**
 * A specification for a blog post
 */
class PostTest extends TestCase
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->author = new Author('Juan', 'juan@example.com');
        $this->blog = new Blog('The Blog');
        $this->post = new Post(
            $this->blog,
            $this->author,
            array(
                'title' => 'My First Post. Great!',
                'summary' => 'This is my first post.',
                'content' => 'This is my first post ever.'
            )
        );
    }

    /**
     * Is not null
     */
    public function testNotNull()
    {
        $this->assertFalse($this->post->isNull());
    }

    /**
     * Can get basic properties
     */
    public function testCanGetBasicProperties()
    {
        $this->assertEquals('My First Post. Great!', $this->post->getTitle());
        $this->assertSame($this->blog, $this->post->getBlog());
        $this->assertSame($this->author, $this->post->getAuthor());
        $this->assertEquals('This is my first post.', $this->post->getSummary());
        $this->assertEquals('This is my first post ever.', $this->post->getContent());
    }

    /**
     * Can edit a post
     */
    public function testCanEditAPost()
    {
        $newTitle = 'The First Post';
        $newSummary = 'The first post.';
        $newContent = 'This is the new content';
        $this->post->edit(array(
            'title' => $newTitle,
            'summary' => $newSummary,
            'content' => $newContent
        ));
        $this->assertEquals($newSummary, $this->post->getSummary(), 'Unable to edit summary');
        $this->assertEquals($newTitle, $this->post->getTitle(), 'Unable to edit title');
        $this->assertEquals($newContent, $this->post->getContent());
    }

    /**
     * Editing a post creates a new revision
     */
    public function testEditingCreatesNewRevision()
    {
        $firstRevision = $this->post->getLatestRevision();
        $this->post->edit(array(
            'title' => 'The First Post',
            'summary' => 'The first post.',
            'content' => 'This is the new content'
        ));
        $this->assertNotSame($firstRevision, $this->post->getLatestRevision());
    }

    /**
     * Editing a post with empty content will not create a new revision
     */
    public function testEditingContentWithEmptyOptionsWillNotCreateNewRevision()
    {
        $firstRevision = $this->post->getLatestRevision();
        $this->post->edit(array(
            'title' => '',
            'summary' => '',
            'content' => ''
        ));
        $this->assertSame($firstRevision, $this->post->getLatestRevision());
        $this->post->edit();
        $this->assertSame($firstRevision, $this->post->getLatestRevision());
    }

    /**
     * Editing just part of the post retains other parts
     *
     * @param string $toEdit the part that is to be edited
     *
     * @dataProvider dataEditingPartOfPostRetainsOtherParts
     */
    public function testEditingPartOfPostRetainsOtherParts($toEdit)
    {
        //'Title'
        $knownParts = array('title', 'summary', 'content');
        unset($knownParts[array_search($toEdit, $knownParts)]);
        $toCheck = array_values($knownParts);
        foreach ($toCheck as $part) {
            $varName = 'old' . ucfirst($part);
            $methodName = 'get' . ucfirst($part);
            $$varName = $this->post->$methodName();
        }
        $this->post->edit(array($toEdit => 'Foo'));
        foreach ($toCheck as $part) {
            $varName = 'old' . ucfirst($part);
            $methodName = 'get' . ucfirst($part);
            $this->assertEquals($$varName, $this->post->$methodName());
        }
    }

    /**
     * Data provider for testEditingPartOfPostRetainsOtherParts
     *
     * @return array parrameters to pass to test
     */
    public function dataEditingPartOfPostRetainsOtherParts()
    {
        return array(
            array('title'),
            array('summary'),
            array('content')
        );
    }

    /**
     * Post is not published by default
     */
    public function testIsNotPublishedByDefault()
    {
        $this->assertFalse($this->post->isPublished());
    }

    /**
     * Can publish a post
     */
    public function testCanPublishAPost()
    {
        $this->post->publish();
        $this->assertTrue($this->post->isPublished());
    }

    /**
     * A non-published post has no published date
     */
    public function testNoPublishDateForUnpublishedPost()
    {
        $this->assertNull($this->post->getPublishDate());
    }

    /**
     * Can get publication date
     */
    public function testCanGetPublishDate()
    {
        $thisDay = new \DateTime;
        $this->post->publish();
        $this->assertEquals($thisDay->format('Y-m-d'), $this->post->getPublishDate()->format('Y-m-d'));
    }

    /**
     * Can get latest revision
     */
    public function testCanGetLatestRevision()
    {
        $this->assertInstanceOf('Asar\Blog\Post\Revision', $this->post->getLatestRevision());
    }

    /**
     * The contents of the latest revision is the same as in creation
     */
    public function testTheContentsOfRevisionIsTheSameAsContentOnCreation()
    {
        $this->assertEquals(
            'This is my first post ever.',
            $this->post->getLatestRevision()->getContent()
        );
    }

    /**
     * The date of last revision date is the same as revision creation date
     */
    public function testTheRevisionDate()
    {
        $this->assertSame(
            $this->post->getLatestRevision()->getDateCreated(),
            $this->post->getLatestRevisionDate()
        );
    }

    /**
     * Creates a post slug
     */
    public function testPostSlug()
    {
        $this->assertEquals('my-first-post-great', $this->post->getSlug());
    }

    /**
     * Slug can be set
     */
    public function testEditPostSlug()
    {
        $this->post->setSlug('FOO Bar!!! Yadayada hee--hee');
        $this->assertEquals('foo-bar-yadayada-hee-hee', $this->post->getSlug());
    }

    /**
     * Can categorize a post
     */
    public function testCanCategorizeAPost()
    {
        $this->category1 = $this->quickMock('Asar\Blog\Category');
        $this->category2 = $this->quickMock('Asar\Blog\Category');
        $this->post->addCategory($this->category1, $this->category2);
        $categories = $this->post->getCategories();
        $this->assertContains($this->category1, $categories);
        $this->assertContains($this->category2, $categories);
    }


}