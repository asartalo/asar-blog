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

use Asar\Blog\Post\Revision;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A blog post common interface
 */
interface PostInterface
{

    /**
     * Returns the id
     *
     * @return integer the post id;
     */
    public function getId();

    /**
     * @return boolean Whether this is a null post
     */
    public function isNull();

    /**
     * Gets the blog title
     *
     * @return string post title
     */
    public function getTitle();

    /**
     * Gets the post summary
     *
     * @return string the post summary
     */
    public function getSummary();

    /**
     * Gets the content
     *
     * @return string the post content
     */
    public function getContent();

    /**
     * Get whether the post has been published
     *
     * @return boolean whether the post has been published or not
     */
    public function isPublished();

    /**
     * Publishes the post
     */
    public function publish();

    /**
     * Gets the author of a post
     *
     * @return Author the post author
     */
    public function getAuthor();

    /**
     * Gets the blog this post belongs to
     *
     * @return Blog the blog
     */
    public function getblog();

    /**
     * Gets the publication date of post
     *
     * @return DateTime the date the post was published
     */
    public function getPublishDate();

    /**
     * Gets the latest revision
     *
     * @return Revision the latest revision
     */
    public function getLatestRevision();

    /**
     * Adds a category to the post
     *
     * @param Category $category the category to add to
     */
    public function addCategory(Category $category);

    /**
     * Returns the categories of the post
     *
     * @return ArrayAccess the post categories
     */
    public function getCategories();

    /**
     * Edit this post
     *
     * @param array $options the post content options
     */
    public function edit($options = array());

    /**
     * Gets the latest revision date
     *
     * @return DateTime the date the post was last revised
     */
    public function getLatestRevisionDate();

}