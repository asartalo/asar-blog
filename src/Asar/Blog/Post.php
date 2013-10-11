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
 * @Table(name="posts", indexes={@Index(name="slug_idx", columns={"slug"})}, uniqueConstraints={@UniqueConstraint(name="slug_blog_idx", columns={"blog_id", "slug"})})
 * @HasLifecycleCallbacks
 */
class Post implements PostInterface
{

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Asar\Blog\Author")
     */
    private $author;

    /**
     * @ManyToOne(targetEntity="Asar\Blog\Blog")
     * @JoinColumn(name="blog_id", referencedColumnName="id")
     */
    private $blog;

    /**
     * @OneToMany(targetEntity="Asar\Blog\Categorization", mappedBy="post")
     */
    private $categorization;

    /**
     * @Column(type="boolean")
     */
    private $published = false;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $datePublished;

    /**
     * @OneToMany(targetEntity="Asar\Blog\Post\Revision", mappedBy="post")
     */
    private $revisions;

    /**
     * @OneToOne(targetEntity="Asar\Blog\Post\Revision", inversedBy="latestRevisionOfPost")
     */
    private $latestRevision;

    /**
     * @Column(type="string", nullable=true)
     **/
    private $slug;

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
        $this->revisions = new ArrayCollection;
        $this->categories = new ArrayCollection;
        $this->newRevision($options);
        if (!$this->slug) {
            $this->slug = $this->createSlug();
        }
    }

    /**
     * Returns the id
     *
     * @return integer the post id;
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean Whether this is a null post
     */
    public function isNull()
    {
        return false;
    }

    private function newRevision($options)
    {
        $revision = new Revision($this, $options);
        $this->revisions[] = $revision;
        $this->latestRevision = $revision;
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
     * Sets the post slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $this->slugify($slug);
    }

    /**
     * Gets the post slug
     *
     * @return string post slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    private function createSlug()
    {
        return $this->slugify($this->getTitle());
    }

    private function slugify($slug)
    {
        return trim(
            preg_replace('/\W+/', '-', strtolower($slug)),
            "- \t\n\r\0\x0B"
        );
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
        return $this->published;
    }

    /**
     * Publishes the post
     */
    public function publish()
    {
        $this->datePublished = new \DateTime;
        $this->published = true;
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
        return $this->latestRevision;
    }

    /**
     * Adds a category to the post
     *
     * @param Category $category the category to add to
     */
    public function addCategory(Category $category)
    {
        $categories = func_get_args();
        foreach ($categories as $category) {
            $this->categories[] = $category;
        }
    }

    /**
     * Returns the categories of the post
     *
     * @return ArrayAccess the post categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Edit this post
     *
     * @param array $options the post content options
     */
    public function edit($options = array())
    {
        if ($this->isOptionsNotEmpty($options)) {
            $fieldsAndDefaults = array(
                'title' => 'getTitle',
                'slug' => 'getSlug',
                'summary' => 'getSummary',
                'content' => 'getContent'
            );
            foreach ($fieldsAndDefaults as $field => $default) {
                if (!isset($options[$field]) || empty($options[$field])) {
                    $options[$field] = $this->$default();
                }
            }
            if ($options['slug']) {
                $this->setSlug($options['slug']);
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