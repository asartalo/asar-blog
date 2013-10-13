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
use Asar\Blog\Categorization;
use Asar\Blog\Post;
use Asar\Blog\Category;

/**
 * A specification for a post categorization
 */
class CategorizationTest extends TestCase
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->post = $this->quickMock('Asar\Blog\Post');
        $this->category = $this->quickMock('Asar\Blog\Category');

        $this->categorization = new Categorization($this->category, $this->post);
    }

    /**
     * Test retrieving a post
     */
    public function testGettingPostFromCategorization()
    {
        $this->assertSame($this->post, $this->categorization->getPost());
    }

    /**
     * Test retrieving a category
     */
    public function testGettingCategoryromCategorization()
    {
        $this->assertSame($this->category, $this->categorization->getCategory());
    }

}