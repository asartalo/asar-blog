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

use Dimple\Container;
use Doctrine\ORM\EntityManager;

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
        return new Container(__DIR__ . DIRECTORY_SEPARATOR . 'services.php');
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
     * @param array  $options other blog options
     *
     * @return Post the new blog post
     */
    public function newPost($author, array $options = array())
    {
        return new Post($this->getCurrentBlog(), $author, $options);
    }


}