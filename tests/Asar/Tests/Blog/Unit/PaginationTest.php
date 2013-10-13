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
use Asar\Blog\Pagination;

/**
 * A specification for a Pagination object
 */
class PaginationTest extends TestCase
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->paginator = $this->quickMock(
            'Doctrine\ORM\Tools\Pagination\Paginator'
        );
        $this->query = $this->quickMock(
            'Doctrine\ORM\AbstractQuery',
            array('getSQL', '_doExecute', 'setMaxResults', 'setFirstResult')
        );
        $this->paginator->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($this->query));
        $this->pagination = new Pagination($this->paginator);
    }

    /**
     * Counting
     */
    public function testGettingTotalItemCount()
    {
        $this->paginator->expects($this->once())
            ->method('count')
            ->will($this->returnValue(3));
        $this->assertEquals(3, $this->pagination->totalItems());
    }

    /**
     * Setting page sizes
     */
    public function testItemsPerPage()
    {
        $this->query->expects($this->once())
            ->method('setMaxResults')
            ->with(3);
        new Pagination($this->paginator, 3);
    }

    /**
     * Getting the total number of pages
     */
    public function testGetNumberOfPages()
    {
        $this->paginator->expects($this->once())
            ->method('count')
            ->will($this->returnValue(24));
        $this->assertEquals(3, $this->pagination->numberOfPages());
    }

    /**
     * Get a page of items
     */
    public function testGetAPage()
    {
        $this->query->expects($this->once())
            ->method('setFirstResult')
            ->with(10);
        $this->assertEquals($this->paginator, $this->pagination->page(2));
    }


}