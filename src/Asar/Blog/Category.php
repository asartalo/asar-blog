<?php
/**
 * This file is part of the Asar Blog library
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\Blog;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * A category for a post
 *
 * @Entity
 * @Table(name="categories")
 */
class Category
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;

    /**
     * @Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ManyToOne(targetEntity="Asar\Blog\Blog")
     * @JoinColumn(name="blog_id", referencedColumnName="id")
     */
    private $blog;

    /**
     * Constructor
     *
     * @param Blog   $blog the blog where this belongs to
     * @param string $name the category name
     */
    public function __construct(Blog $blog, $name)
    {
        $this->blog = $blog;
        $this->name = $name;
    }

    /**
     * Returns the id
     *
     * @return integer the catogory id;
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the name of the category
     *
     * @return string the name of the category
     */
    public function getName()
    {
        return $this->name;
    }
}