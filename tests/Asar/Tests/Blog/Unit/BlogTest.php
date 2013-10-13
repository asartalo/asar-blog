<?php
/**
 * This file is part of the Asar Blog library
 *
 * (c) Wayne Duran <asartalo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\Tests\Blog\Unit;

use Asar\TestHelper\TestCase;
use Asar\Blog\Blog;

/**
 * A specification for a blog
 */
class BlogTest extends TestCase
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->blog = new Blog('The Blog', array('description' => 'This is a blog.'));
    }

    /**
     * Can get basic properties
     */
    public function testCanGetBasicProperties()
    {
        $this->assertEquals('The Blog', $this->blog->getName());
        $this->assertEquals('This is a blog.', $this->blog->getDescription());
    }

}