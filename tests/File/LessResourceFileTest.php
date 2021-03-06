<?php

namespace Chigi\Chiji\File;

use Chigi\Component\IO\File;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-03-17 at 16:23:22.
 */
class LessResourceFileTest extends \PHPUnit_Framework_TestCase {

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory("Resources"));
        vfsStream::copyFromFileSystem(dirname(__DIR__) . "/Resources", vfsStreamWrapper::getRoot());
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers Chigi\Chiji\File\LessResourceFile::getStamp
     * @todo   Implement testGetStamp().
     */
    public function testGetStamp() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     */
    public function testFindComments() {
        /* @var $resource LessResourceFile */
    }

}
