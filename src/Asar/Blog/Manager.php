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

use Dimple\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Manages blog
 */
class Manager
{

    private $entityManager;

    private $currentBlog;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Static method factory
     *
     * @return Manager
     */
    public static function createManager()
    {
        $container = self::createContainer();

        return new self($container->get('doctrine.entityManager'));
    }

    /**
     * Static method factory for testing
     *
     * @return Manager
     */
    public static function createManagerForTest()
    {
        $container = self::createContainer();
        $container['isTestMode'] = true;

        return new self($container->get('doctrine.entityManager'));
    }

    protected static function createContainer()
    {
        return new Container(self::getServicesPath());
    }

    /**
     * Get the services definition
     *
     * @return string the path to the services definition file
     */
    public static function getServicesPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'services.php';
    }

    /**
     * Returns the entity manager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Creates a new blog
     *
     * @param string $name    the blog name
     * @param array  $options some blog options
     *
     * @return Blog the newly created blog
     */
    public function newBlog($name, array $options=array())
    {
        $blog = new Blog($name, $options);
        $this->getEntityManager()->persist($blog);

        return $blog;
    }

    /**
     * Commit changes to the database
     */
    public function commit()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Gets a blog by name or id
     *
     * @param mixed $id the id or name of the blog
     *
     * @return Blog
     */
    public function getBlog($id)
    {
        $repository = $this->getEntityManager()->getRepository('Asar\Blog\Blog');
        if (is_int($id)) {
            return $repository->find($id);
        }

        return $repository->findOneBy(array('name' => $id));
    }

    /**
     * Creates a new author
     *
     * @param string $name  the author name
     * @param string $email the author's email address
     *
     * @return Author the newly created author
     */
    public function newAuthor($name, $email)
    {
        $author = new Author($name, $email);
        $this->getEntityManager()->persist($author);

        return $author;
    }

    /**
     * Gets an author based on name
     *
     * @param mixed $name the name of the author
     *
     * @return Author
     */
    public function getAuthor($name)
    {
        return $this->getEntityManager()
                    ->getRepository('Asar\Blog\Author')
                    ->findOneBy(array('name' => $name));
    }

    /**
     * Start managing a blog
     *
     * @param mixed $id blog name or id
     */
    public function manage($id)
    {
        $this->currentBlog = $this->getBlog($id);
    }

    /**
     * Retrieves the currently managed blog
     *
     * @return Blog the currently managed blog
     */
    public function getCurrentBlog()
    {
        return $this->currentBlog;
    }

    /**
     * Creates a new blog post
     *
     * @param Author $author  the blog post's author
     * @param array  $options post options
     *
     * @return Post the new blog post
     */
    public function newPost($author, array $options = array())
    {
        $post = new Post($this->getCurrentBlog(), $author, $options);
        $this->getEntityManager()->persist($post->getLatestRevision());
        $this->getEntityManager()->persist($post);

        return $post;
    }

    /**
     * Creates a category
     *
     * @param string $name the category name
     *
     * @return Category the new category
     */
    public function newCategory($name)
    {
        $category = new Category($this->getCurrentBlog(), $name);
        $this->getEntityManager()->persist($category);

        return $category;
    }

    /**
     * Adds a post to a category
     *
     * @param string $categoryName the name of category to add to
     * @param Post   $post         the blog post
     */
    public function addToCategory($categoryName, Post $post)
    {
        $category = $this->getEntityManager()
            ->getRepository('Asar\Blog\Category')
            ->findOneBy(array('name' => $categoryName));
        $categorization = new Categorization($category, $post);
        $this->getEntityManager()->persist($categorization);
    }

    /**
     * Retrieves a post
     *
     * @param mixed $id the post id or slug
     *
     * @return Post the post
     */
    public function getPost($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        if (is_int($id) || is_object($id)) {
            $reference = 'id';
        } else {
            $reference = 'slug';
        }

        $qb->select('post')
           ->from('Asar\Blog\Post', 'post')
           ->where("post.blog = :blog AND post.$reference = :post")
           ->setParameter(':blog', $this->getCurrentBlog()->getId())
           ->setParameter(':post', $id);

        $result = $qb->getQuery()->getResult();
        if (count($result) == 0) {
            return new NullPost;
        }

        return $result[0];
    }

    /**
     * Edits a post
     *
     * @param mixed $post    the post or post id
     * @param array $options post options
     */
    public function editPost($post, array $options)
    {
        $postToEdit = $this->getPost($post);
        $postToEdit->edit($options);
        $newRevision = $post->getLatestRevision();
        $this->getEntityManager()->persist($newRevision);
    }

    /**
     * Retrieves all posts
     *
     * @param array $options post query options
     *
     * @return ArrayCollection the posts in the current blog
     */
    public function getPosts($options = array())
    {
        $paginate = false;

        if (isset($options['paginateBy'])) {
            $perPage = $options['paginateBy'];
            $paginate = true;
        }
        unset($options['paginateBy']);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('post')
           ->from('Asar\Blog\Post', 'post')
           ->orderBy('post.datePublished', 'DESC');
        $this->generatePostQueryOptions($qb, $options);

        if ($paginate) {
            return new Pagination(
                new Paginator($qb->getQuery(), false),
                $perPage
            );
        }

        return $qb->getQuery()->getResult();
    }

    protected function generatePostQueryOptions($qb, $options)
    {
        $whereClause = 'post.blog = :blog';
        foreach ($options as $field => $value) {
            $fieldName = $this->generateFieldName('post', $field);
            $fieldParam = $this->generateFieldParam($field);
            $whereClause .= " AND $fieldName = :$fieldParam";
        }
        $qb->where($whereClause)
           ->setParameter(':blog', $this->getCurrentBlog()->getId());
        foreach ($options as $field => $value) {
            $fieldParam = $this->generateFieldParam($field);
            $qb->setParameter(":$fieldParam", $value);
        }
    }

    protected function generateFieldName($prefix, $field)
    {
        return strpos($field, '.') > 0 ? $field : "$prefix.$field";
    }

    protected function generateFieldParam($field)
    {
        if (strpos($field, '.') > -1) {
            return  preg_replace("/\.(.)/e", "strtoupper('\\1')", $field);
        }

        return $field;
    }

    /**
     * Retrieves all post for a category
     *
     * @param string $categoryName the name of the category
     * @param array  $options      post query options
     *
     * @return ArrayCollection $posts the posts in the category
     */
    public function getPostsInCategory($categoryName, $options = array())
    {
        $paginate = false;

        if (isset($options['paginateBy'])) {
            $perPage = $options['paginateBy'];
            $paginate = true;
        }
        unset($options['paginateBy']);

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('post')
           ->from('Asar\Blog\Post', 'post')
           ->leftJoin('post.categorization', 'categorization')
           ->leftJoin('categorization.category', 'category')
           ->orderBy('post.datePublished', 'DESC');
        $options = array_merge($options, array('category.name' => $categoryName));
        $this->generatePostQueryOptions($qb, $options);

        if ($paginate) {
            return new Pagination(
                new Paginator($qb->getQuery(), false),
                $perPage
            );
        }

        return $qb->getQuery()->getResult();
    }

}