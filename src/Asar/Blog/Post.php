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
 * A blog post
 *
 * @Entity
 * @Table(name="posts")
 * @HasLifecycleCallbacks
 */
class Post
{

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var int
     **/
    private $id;

    /**
     * @Column(type="string")
     * @var string
     **/
    private $title;

    /**
     * @OneToOne(targetEntity="Asar\Blog\Author")
     **/
    private $author;


    /**
     * @ManyToOne(targetEntity="Asar\Blog\Blog")
     * @JoinColumn(name="blog_id", referencedColumnName="id")
     */
    private $blog;

    /**
     * @Column(type="text")
     * @var text
     **/
    private $summary;

    /**
     * @Column(type="text")
     * @var text
     **/
    private $content;

    /**
     * @Column(type="boolean")
     * @var boolean
     **/
    private $publishStatus = false;

    /**
     * @Column(type="datetime")
     * @var datetime
     **/
    private $datePublished;

    /**
     * @OneToMany(targetEntity="Asar\Blog\Post\Revision", mappedBy="post")
     * @var Asar\Blog\Post\Revision[]
     **/
    private $revisions;

    private $revisionParts = array('title', 'summary', 'content');

    /**
     * Constructor
     *
     * @param Blog   $blog    the blog this belongs to
     * @param Author $author  the post author
     * @param array  $options other options and properties
     */
    public function __construct(Blog $blog, Author $author, array $options=array())
    {
        $this->blog = $blog;
        $this->author = $author;
        if (isset($options['summary'])) {
            $this->summary = $options['summary'];
        }
        $this->revisions = new ArrayCollection;
        $this->newRevision($options);
    }

    private function newRevision($options)
    {
        $this->revisions[] = new Revision(
            $this, $options
        );
    }

    /**
     * Gets the blog title
     *
     * @return string post title
     */
    public function getTitle()
    {
        return $this->getLatestRevision()->getTitle();
    }

    /**
     * Gets the post summary
     *
     * @return string the post summary
     */
    public function getSummary()
    {
        return $this->getLatestRevision()->getSummary();
    }

    /**
     * Gets the content
     *
     * @return string the post content
     */
    public function getContent()
    {
        return $this->getLatestRevision()->getContent();
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
        $this->datePublished = new \DateTime;
        $this->publishStatus = true;
    }

    /**
     * Gets the author of a post
     *
     * @return Author the post author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Gets the blog this post belongs to
     *
     * @return Blog the blog
     */
    public function getblog()
    {
        return $this->blog;
    }

    /**
     * Gets the publication date of post
     *
     * @return DateTime the date the post was published
     */
    public function getPublishDate()
    {
        return $this->datePublished;
    }

    /**
     * Gets the latest revision
     *
     * @return Revision the latest revision
     */
    public function getLatestRevision()
    {
        return $this->revisions->last();
    }

    /**
     * Edit this post
     *
     * @param array $options the post content options
     */
    public function edit($options = array())
    {
        if ($this->isOptionsNotEmpty($options)) {
            if (!isset($options['title'])) {
                $options['title'] = $this->getTitle();
            }
            if (!isset($options['summary'])) {
                $options['summary'] = $this->getSummary();
            }
            if (!isset($options['content'])) {
                $options['content'] = $this->getContent();
            }
            $this->newRevision($options);
        }
    }

    private function isOptionsNotEmpty($options)
    {
        if (empty($options)) {
            return false;
        }
        $notCompletelyEmpty = false;
        foreach ($this->revisionParts as $part) {
            if (isset($options[$part]) && !empty($options[$part])) {
                $notCompletelyEmpty = true;
                break;
            }
        }

        return $notCompletelyEmpty;
    }

    /**
     * Gets the latest revision date
     *
     * @return DateTime the date the post was last revised
     */
    public function getLatestRevisionDate()
    {
        return $this->getLatestRevision()->getDateCreated();
    }

}