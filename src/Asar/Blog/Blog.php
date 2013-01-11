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
 * A blog
 *
 * @Entity
 * @Table(name="blogs")
 */
class Blog
{

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     **/
    private $id;

    /**
     * @Column(type="string", length=255, unique=true)
     **/
    private $name;

    /**
     * @Column(type="text")
     **/
    private $description;

    /**
     * Constructor
     *
     * @param string $name    the author name
     * @param array  $options other options and properties
     */
    public function __construct($name, array $options=array())
    {
        $this->name = $name;
        if (isset($options['description'])) {
            $this->description = $options['description'];
        }
    }

    /**
     * Gets the blog name
     *
     * @return string blog name
     */
    public function getName()
    {
        return $this->name;
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

}