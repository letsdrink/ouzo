<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Routing\Route;
use Ouzo\Uri\UriHelperGenerator;
use Ouzo\Utilities\Path;

class UriHelperGeneratorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Route::$routes = array();
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForGet()
    {
        //given
        Route::get('/users/show_item', 'users#show_item');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
<?php
function checkParameter(\$parameter)
{
    if (!isset(\$parameter)) {
        throw new \InvalidArgumentException("Missing parameters");
    }
}

function showItemUsersPath()
{
    return url("/users/show_item");
}
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForPost()
    {
        //given
        Route::post('/users/save', 'users#save');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
<?php
function checkParameter(\$parameter)
{
    if (!isset(\$parameter)) {
        throw new \InvalidArgumentException("Missing parameters");
    }
}

function saveUsersPath()
{
    return url("/users/save");
}
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForAny()
    {
        //given
        Route::any('/users/get_duplicated', 'users#get_duplicated');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
<?php
function checkParameter(\$parameter)
{
    if (!isset(\$parameter)) {
        throw new \InvalidArgumentException("Missing parameters");
    }
}

function getDuplicatedUsersPath()
{
    return url("/users/get_duplicated");
}
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperWhenMultipleBindParameters()
    {
        //given
        Route::get('/users/show/id/:id/call_id/:call_id', 'users#show');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
<?php
function checkParameter(\$parameter)
{
    if (!isset(\$parameter)) {
        throw new \InvalidArgumentException("Missing parameters");
    }
}

function showUsersPath(\$id, \$call_id)
{
    checkParameter(\$id);
    checkParameter(\$call_id);
    return url("/users/show/id/\$id/call_id/\$call_id");
}
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForCustomRouteName()
    {
        //given
        Route::get('/users/show_item', 'users#show_item', array('as' => 'user_item'));

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
<?php
function checkParameter(\$parameter)
{
    if (!isset(\$parameter)) {
        throw new \InvalidArgumentException("Missing parameters");
    }
}

function userItemPath()
{
    return url("/users/show_item");
}
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForResource()
    {
        //given
        Route::resource('users');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
<?php
function checkParameter(\$parameter)
{
    if (!isset(\$parameter)) {
        throw new \InvalidArgumentException("Missing parameters");
    }
}

function usersPath()
{
    return url("/users");
}

function freshUserPath()
{
    return url("/users/fresh");
}

function editUserPath(\$id)
{
    checkParameter(\$id);
    return url("/users/\$id/edit");
}

function userPath(\$id)
{
    checkParameter(\$id);
    return url("/users/\$id");
}
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldSaveGeneratedUriHelperInFile()
    {
        //given
        Route::resource('users');
        $fileName = uniqid() . '_GeneratedUriHelper.php';
        $path = Path::joinWithTemp($fileName);
        $generator = UriHelperGenerator::generate();

        //when
        $generator->saveToFile($path);

        //then
        $this->assertFileExists($path);
        $this->assertEquals($generator->getGeneratedFunctions(), file_get_contents($path));
        unlink($path);
    }

    /**
     * @test
     */
    public function shouldGenerateCorrectNestedResources()
    {
        //given
        Route::get('/api/users/:id/orders', 'api/users#orders');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
<?php
function checkParameter(\$parameter)
{
    if (!isset(\$parameter)) {
        throw new \InvalidArgumentException("Missing parameters");
    }
}

function ordersUsersApiPath(\$id)
{
    checkParameter(\$id);
    return url("/api/users/\$id/orders");
}
FUNCT;
        $this->assertEquals($expected, $generated);
    }
}
