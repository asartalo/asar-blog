<?php
/**
 * This file is part of the Asar Blog library
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asar\Tests\Blog\Unit\Post;

use Asar\TestHelper\TestCase;
use Asar\Blog\Post\Revision;


/**
 * A specification for a blog post revision
 */
class RevisionTest extends TestCase
{

    /**
     * Setup
     */
    public function setUp()
    {
        $this->post = $this->quickMock('Asar\Blog\Post');
        $this->revision = new Revision('foo', $this->post);
    }

    /**
     * Can get the content
     */
    public function testGettingContent()
    {
        $this->assertEquals('foo', $this->revision->getContent());
    }


    /**
     * Sets creation date
     */
    public function testSetsCreationDate()
    {
        $this->assertInstanceOf('DateTime', $this->revision->getDateCreated());
    }

    /**
     * Get teh post
     */
    public function testGetsThePost()
    {
        $this->assertSame($this->post, $this->revision->getPost());
    }

}