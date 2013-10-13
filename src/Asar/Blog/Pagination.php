<?php
/**
 * This file is part of the Asar Blog library
 *
 * (c) Wayne Duran <asartalo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\Blog;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Countable;

/**
 * A pagination helper
 */
class Pagination
{

    private $paginator;

    private $perPage;

    /**
     * @param Paginator $paginator the doctrine paginator object
     * @param intenger  $perPage   the number of items per page
     */
    public function __construct(Paginator $paginator, $perPage = 10)
    {
        $this->paginator = $paginator;
        $this->perPage = $perPage;
        $this->paginator->getQuery()->setMaxResults($this->perPage);
    }

    /**
     * Get the total number of items
     *
     * @return integer the total number of items
     */
    public function totalItems()
    {
        return $this->paginator->count();
    }

    /**
     * @return integer the number of pages
     */
    public function numberOfPages()
    {
        $total = $this->paginator->count();

        return ceil($total/$this->perPage);
    }

    /**
     * Get the items of a page
     *
     * @param integer $page the page number
     *
     * @return Paginator the page
     */
    public function page($page)
    {
        $startAt = ($page - 1) * $this->perPage;
        $this->paginator->getQuery()->setFirstResult($startAt);

        return $this->paginator;
    }


}