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
use Asar\Blog\Category;

/**
 * A specification for a post category
 */
class CategoryTest extends TestCase
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->blog = $this->quickMock('Asar\Blog\Blog');
        $this->category = new Category($this->blog, 'fooCategory');
    }

    /**
     * Can get the category name
     */
    public function testCanGetTheName()
    {
        $this->assertEquals('fooCategory', $this->category->getName());
    }

}