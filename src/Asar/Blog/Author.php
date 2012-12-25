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
     */
    private $id;

    /**
     * @Column(type="string")
     */
    private $name;

    /**
     * @Column(type="string", length=250, unique=true)
     */
    private $email;

    /**
     * Constructor
     *
     * @param string $name  the author name
     * @param string $email the author's email address
     */
    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
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

    /**
     * Gets the email
     *
     * @return string the author's email address
     */
    public function getEmail()
    {
        return $this->email;
    }

}