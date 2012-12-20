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

use Asar\Blog\Author;

/**
 * A specification for a blog author
 */
class AuthorTest extends TestCase
{
    
    /**
     * Setup
     */
    public function setUp()
    {
        $this->author = new Author('Juan');
    }
    
    /**
     * Can get name
     */
    public function testCanGetName()
    {
        $this->assertEquals('Juan', $this->author->getName());
    }
    
}