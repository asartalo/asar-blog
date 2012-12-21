<?php
/**
 * This file is part of the Asar Blog library
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\Blog\Post;

use Asar\Blog\Post;
use DateTime;

/**
 * A blog post revision
 *
 * @Entity
 * @Table(name="revisions")
 */
class Revision
{

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var int
     **/
    private $id;

    /**
     * @Column(type="text")
     * @var text
     **/
    private $content;

    /**
     * @ManyToOne(targetEntity="Asar\Blog\Post", inversedBy="revisions")
     **/
    private $post;

    /**
     * @Column(type="datetime")
     * @var datetime
     **/
    private $created;


    /**
     * Constructor
     *
     * @param string $content the contents of this revision
     * @param Post   $post    the parent post
     */
    public function __construct($content, Post $post)
    {
        $this->content = $content;
        $this->post = $post;
        $this->created = new DateTime;
    }

    /**
     * Returns the content of the revision
     *
     * @return string the content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Returns the parent post
     *
     * @return Post the parent post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Returns the date the revision was created
     *
     * @return DateTime
     */
    public function getDateCreated()
    {
        return $this->created;
    }

}