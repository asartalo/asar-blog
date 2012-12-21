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
 * A blog author
 *
 * @Entity
 * @Table(name="authors")
 */
class Author
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
    private $name;

    /**
     * Constructor
     *
     * @param string $name the author name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the name
     *
     * @return string author name
     */
    public function getName()
    {
        return $this->name;
    }

}