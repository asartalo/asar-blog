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
        $this->author = new Author('Juan', 'juan@example.com');
    }

    /**
     * Can get name
     */
    public function testCanGetName()
    {
        $this->assertEquals('Juan', $this->author->getName());
    }

    /**
     * Can get email
     */
    public function testGetEmail()
    {
        $this->assertEquals('juan@example.com', $this->author->getEmail());
    }

}