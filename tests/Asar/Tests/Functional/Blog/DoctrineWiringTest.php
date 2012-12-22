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

    protected function createBasicBlog()
    {
        return $this->manager->newBlog('FooBlog', array('description' => 'The foo blog'));
    }

    /**
     * Test creating a blog
     */
    public function testCreatingABlog()
    {
        $blog = $this->createBasicBlog();
        $this->assertEquals('FooBlog', $blog->getName());
        $this->assertEquals('The foo blog', $blog->getDescription());
    }

    /**
     * Test commits changes to blog
     */
    public function testCommitsChanges()
    {
        $blog = $this->createBasicBlog();
        $this->manager->commit();
        //$this->manager = Manager::createManager();
        $blog = $this->manager->getBlog('FooBlog');
        $this->assertEquals('FooBlog', $blog->getName());
        $this->assertEquals('The foo blog', $blog->getDescription());
    }

    /**
     * Test getting a blog by id
     */
    public function testGetABlogById()
    {
        $blog = $this->createBasicBlog();
        $this->manager->commit();
        //$this->manager = Manager::createManager();
        $blog = $this->manager->getBlog(1);
        $this->assertEquals('FooBlog', $blog->getName());
        $this->assertEquals('The foo blog', $blog->getDescription());
    }

    private function createTestAuthor()
    {
        return $this->manager->newAuthor('Pedro');
    }

    /**
     * A new author
     */
    public function testNewAuthor()
    {
        $author = $this->createTestAuthor();
        $this->assertInstanceOf('Asar\Blog\Author', $author);
        $this->assertEquals('Pedro', $author->getName());
    }

    /**
     * Getting an author by name
     */
    public function testGettingAnAuthor()
    {
        $blog = $this->createBasicBlog();
        $this->createTestAuthor();
        $this->manager->commit();
        $author = $this->manager->getAuthor('Pedro');
        $this->assertEquals('Pedro', $author->getName());
    }

    /**
     * Writing a post
     */
    public function testWritingAPost()
    {
        $this->createBasicBlog();
        $this->markTestIncomplete();
        $this->manager->setBlog('FooBlog');
        //$this->manager->
    }

}