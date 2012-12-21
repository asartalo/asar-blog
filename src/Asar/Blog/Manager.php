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
        $container = new Container(__DIR__ . DIRECTORY_SEPARATOR . 'services.php');

        return new self($container->get('doctrine.entityManager'));
    }

    /**
     * Static method factory for testing
     *
     * @return Manager
     */
    public static function createManagerForTest()
    {
        $container = new Container(__DIR__ . DIRECTORY_SEPARATOR . 'services.php');
        $container['isTestMode'] = true;

        return new self($container->get('doctrine.entityManager'));
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
        if (is_int($id)) {
            return $this->getEntityManager()
                    ->getRepository('Asar\Blog\Blog')
                    ->find($id);
        }

        return $this->getEntityManager()
                    ->getRepository('Asar\Blog\Blog')
                    ->findOneBy(array('name' => $id));
    }


}