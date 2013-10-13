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
use Asar\Blog\NullPost;
use Asar\Blog\Blog;
use Asar\Blog\Author;

/**
 * A specification for a null blog post
 *
 * Represents a null object
 */
class NullPostTest extends TestCase
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->post = new NullPost;
    }

    /**
     * Is null
     */
    public function testIsNull()
    {
        $this->assertTrue($this->post->isNull());
    }


}