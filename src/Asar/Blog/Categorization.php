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

/**
 * Bridge class representing which post a category belongs to
 *
 * @Entity
 * @Table(name="categorizations")
 */
class Categorization
{

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Asar\Blog\Post")
     */
    private $post;

    /**
     * @ManyToOne(targetEntity="Asar\Blog\Category")
     */
    private $category;

    /**
     * Constructor
     *
     * @param Category $category the category
     * @param Post     $post     a blog post
     */
    public function __construct(Category $category, Post $post)
    {
        $this->post = $post;
        $this->category = $category;
    }

    /**
     * Returns the post for this categorization
     *
     * @return Post the post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Returns the category for this categorization
     *
     * @return Category $category the category
     */
    public function getCategory()
    {
        return $this->category;
    }
}