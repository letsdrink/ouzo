<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tools\Controller\Template\ActionGenerator;
use Ouzo\Tools\Controller\Template\ViewGenerator;
use Ouzo\Utilities\DeleteDirectory;
use Ouzo\Utilities\Path;

class ViewGeneratorTest extends PHPUnit_Framework_TestCase
{
    private $path;

    protected function setUp()
    {
        parent::setUp();
        $this->path = Path::joinWithTemp('users');
    }

    protected function tearDown()
    {
        DeleteDirectory::recursive($this->path);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldCreateDirectoryForController()
    {
        //given
        $viewGenerator = new ViewGenerator('users', $this->path);

        //when
        $exists = $viewGenerator->createViewDirectoryIfNotExists();

        //then
        $this->assertTrue($exists);
        $this->assertFileExists($this->path);
    }

    /**
     * @test
     */
    public function shouldAppendViewFile()
    {
        //given
        $viewGenerator = new ViewGenerator('users', $this->path);
        $viewGenerator->createViewDirectoryIfNotExists();
        $actionGenerator = new ActionGenerator('save');

        //when
        $appendAction = $viewGenerator->appendAction($actionGenerator);

        //then
        $this->assertTrue($appendAction);
        $this->assertFileExists(Path::join($this->path, $actionGenerator->getActionViewFile()));
    }

    /**
     * @test
     */
    public function shouldNotAppendFileWhenExists()
    {
        //given
        $viewGenerator = new ViewGenerator('users', $this->path);
        $viewGenerator->createViewDirectoryIfNotExists();
        $actionGenerator = new ActionGenerator('save');
        $viewGenerator->appendAction($actionGenerator);

        //when
        $appendAction = $viewGenerator->appendAction($actionGenerator);

        //then
        $this->assertFalse($appendAction);
    }
}
