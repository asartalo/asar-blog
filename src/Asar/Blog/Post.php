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

/**
 * A blog post
 */
class Post
{
    
    private $title;
    
    private $description;
    
    private $content;
    
    private $publishStatus = false;
    
    /**
     * Constructor
     * 
     * @param string $title   the title of the post
     * @param array  $options other options and properties
     */
    public function __construct($title, array $options=array())
    {
        $this->title = $title;
        if (isset($options['description'])) {
            $this->description = $options['description'];
        }
        if (isset($options['content'])) {
            $this->content = $options['content'];
        }
    }
    
    /**
     * Gets the blog title
     * 
     * @return string post title
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Gets the description
     * 
     * @return string the blog description
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Gets the content
     * 
     * @return string the post content
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Get whether the post has been published
     * 
     * @return boolean whether the post has been published or not
     */
    public function isPublished()
    {
        return $this->publishStatus;
    }
    
    /**
     * Publishes the post
     */
    public function publish()
    {
        $this->publishStatus = true;
    }
}