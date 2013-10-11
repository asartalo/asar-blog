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
     **/
    private $id;

    /**
     * @Column(type="string")
     **/
    private $title;

    /**
     * @Column(type="text")
     **/
    private $summary;

    /**
     * @Column(type="text")
     **/
    private $content;

    /**
     * @ManyToOne(targetEntity="Asar\Blog\Post", inversedBy="revisions")
     **/
    private $post;

    /**
     * @OneToOne(targetEntity="Asar\Blog\Post", mappedBy="latestRevision")
     **/
    private $latestRevisionOfPost;

    /**
     * @Column(type="datetime")
     **/
    private $created;


    /**
     * Constructor
     *
     * @param Post  $post    the parent post
     * @param array $options other options for this revision
     */
    public function __construct(Post $post, array $options)
    {
        $this->post = $post;
        $this->title = $options['title'];
        $this->summary = $options['summary'];
        $this->content = $options['content'];
        $this->created = new DateTime;
    }

    /**
     * Returns the title of the revision
     *
     * @return string the revision title
     */
    public function getTitle()
    {
        return $this->title;
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
     * Returns the summary for the revision
     *
     * @return string the summary
     */
    public function getSummary()
    {
        return $this->summary;
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