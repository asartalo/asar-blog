<?php
/**
 * This file is part of the Asar Blog library
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\Tests\Functional\Blog;

use Asar\Blog\Manager;
use Asar\Tests\Unit\Blog\TestCase;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Some basic tests for testing doctrine wiring
 */
class DoctrineWiringTest extends TestCase
{


    /**
     * Setup
     */
    public function setUp()
    {
        $this->manager = Manager::createManagerForTest();

        // Retrieve the Doctrine 2 entity manager
        $this->em = $this->manager->getEntityManager();

        // Instantiate the schema tool
        $tool = new SchemaTool($this->em);

        // Retrieve all of the mapping metadata
        $classes = $this->em->getMetadataFactory()->getAllMetadata();

        // Delete the existing test database schema
        $tool->dropSchema($classes);

        // Create the test database schema
        $tool->createSchema($classes);
    }

    /**
     * Test creating a blog
     */
    public function testCreatingABlog()
    {
        $blog = $this->manager->newBlog('FooBlog', array('description' => 'The foo blog'));
        $this->assertEquals('FooBlog', $blog->getName());
        $this->assertEquals('The foo blog', $blog->getDescription());
    }

    /**
     * Test commits changes to blog
     */
    public function testCommitsChanges()
    {
        $this->manager->newBlog('FooBlog', array('description' => 'The foo blog'));
        $this->manager->commit();
        //$this->manager = Manager::createManager();
        $blog = $this->manager->getBlog('FooBlog');
        $this->assertEquals('FooBlog', $blog->getName());
        $this->assertEquals('The foo blog', $blog->getDescription());
    }

}