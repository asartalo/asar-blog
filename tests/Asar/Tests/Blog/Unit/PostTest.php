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
            'My First Post',
            $this->blog,
            $this->author,
            array(
                'description' => 'This is my first post.',
                'content'     => 'This is my first post ever.'
            )
        );
    }

    /**
     * Can get basic properties
     */
    public function testCanGetBasicProperties()
    {
        $this->assertEquals('My First Post', $this->post->getTitle());
        $this->assertSame($this->blog, $this->post->getBlog());
        $this->assertSame($this->author, $this->post->getAuthor());
        $this->assertEquals('This is my first post.', $this->post->getDescription());
        $this->assertEquals('This is my first post ever.', $this->post->getContent());
    }

    /**
     * Can modify description
     */
    public function testCanModifyDescription()
    {
        $newDescription = 'The first post.';
        $this->post->setDescription($newDescription);
        $this->assertEquals($newDescription, $this->post->getDescription());
    }

    /**
     * Can modify title
     */
    public function testCanModifyTitle()
    {
        $newTitle = 'The First Post';
        $this->post->setTitle($newTitle);
        $this->assertEquals($newTitle, $this->post->getTitle());
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
     * Creates another revision when content is edited
     */
    public function testCreatesNewRevisionWhenEditingContent()
    {
        $firstRevision = $this->post->getLatestRevision();
        $this->post->setContent('foo');
        $this->assertEquals('foo', $this->post->getContent());
        $this->assertNotSame($firstRevision, $this->post->getLatestRevision());
        $this->assertEquals('foo', $this->post->getLatestRevision()->getContent());
    }

}