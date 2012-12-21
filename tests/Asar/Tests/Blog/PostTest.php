<?php
/**
 * This file is part of the Asar Blog library
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\Tests\Blog;

use Asar\Blog\Post;
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
        /*
            publication date
            last edited
        */

        $this->author = new Author('Juan');
        $this->post = new Post(
            'My First Post',
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
        $this->assertEquals('This is my first post.', $this->post->getDescription());
        $this->assertEquals($this->author, $this->post->getAuthor());
        $this->assertEquals('This is my first post ever.', $this->post->getContent());
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
    
}