<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Utilities;

use Ouzo\Config;
use Ouzo\Routing\Route;
use Ouzo\Uri\JsUriHelperGenerator;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JsUriHelperGeneratorTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        parent::setUp();
        Route::clear();
        $this->path = Path::joinWithTemp(uniqid() . '_generated_uri_helper.js');
        Config::overrideProperty("global", "prefix_system")->with("/app");
    }

    protected function tearDown(): void
    {
        Config::revertProperty("global", "prefix_system");
        if (Files::exists($this->path)) {
            unlink($this->path);
        }
        parent::tearDown();
    }

    #[Test]
    public function shouldGenerateUriHelperForGet()
    {
        //given
        Route::get('/users/show_item', 'Controller\\UsersController', 'show_item');

        //when
        $generated = JsUriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<EXPECTED
function checkParameter(parameter) {
    if (parameter === null) {
        throw new Error("Uri helper: Missing parameters");
    }
}

function showItemUsersPath() {
    return "/app/users/show_item";
}\n
EXPECTED;
        $this->assertEquals($expected, $generated);
    }

    #[Test]
    public function shouldGenerateUriHelperForPost()
    {
        //given
        Route::post('/users/save', 'Controller\\UsersController', 'save');

        //when
        $generated = JsUriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
function checkParameter(parameter) {
    if (parameter === null) {
        throw new Error("Uri helper: Missing parameters");
    }
}

function saveUsersPath() {
    return "/app/users/save";
}\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    #[Test]
    public function shouldGenerateUriHelperForAny()
    {
        //given
        Route::any('/users/get_duplicated', 'Controller\\UsersController', 'get_duplicated');

        //when
        $generated = JsUriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
function checkParameter(parameter) {
    if (parameter === null) {
        throw new Error("Uri helper: Missing parameters");
    }
}

function getDuplicatedUsersPath() {
    return "/app/users/get_duplicated";
}\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    #[Test]
    public function shouldGenerateUriHelperWhenMultipleBindParameters()
    {
        //given
        Route::get('/users/show/id/:id/call_id/:call_id', 'Controller\\UsersController', 'show');

        //when
        $generated = JsUriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
function checkParameter(parameter) {
    if (parameter === null) {
        throw new Error("Uri helper: Missing parameters");
    }
}

function showUsersPath(id, call_id) {
    checkParameter(id);
    checkParameter(call_id);
    return "/app/users/show/id/" + id + "/call_id/" + call_id + "";
}\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    #[Test]
    public function shouldGenerateUriHelperForCustomRouteName()
    {
        //given
        Route::get('/users/show_item', 'Controller\\UsersController', 'show_item', ['as' => 'user_item']);

        //when
        $generated = JsUriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
function checkParameter(parameter) {
    if (parameter === null) {
        throw new Error("Uri helper: Missing parameters");
    }
}

function userItemPath() {
    return "/app/users/show_item";
}\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    #[Test]
    public function shouldGenerateUriHelperForResource()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');

        //when
        $generated = JsUriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
function checkParameter(parameter) {
    if (parameter === null) {
        throw new Error("Uri helper: Missing parameters");
    }
}

function usersPath() {
    return "/app/users";
}

function userPath(id) {
    checkParameter(id);
    return "/app/users/" + id + "";
}

function editUserPath(id) {
    checkParameter(id);
    return "/app/users/" + id + "/edit";
}

function freshUserPath() {
    return "/app/users/fresh";
}\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    #[Test]
    public function shouldGenerateCorrectNestedResources()
    {
        //given
        Route::get('/api/users/:id/orders', 'Controller\\Api\\UsersController', 'orders');

        //when
        $generated = JsUriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
function checkParameter(parameter) {
    if (parameter === null) {
        throw new Error("Uri helper: Missing parameters");
    }
}

function ordersUsersApiPath(id) {
    checkParameter(id);
    return "/app/api/users/" + id + "/orders";
}\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }

    #[Test]
    public function shouldSaveGeneratedUriHelperInFile()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');
        $generator = JsUriHelperGenerator::generate();

        //when
        $generator->saveToFile($this->path);

        //then
        $this->assertFileExists($this->path);
        $this->assertEquals($generator->getGeneratedFunctions(), file_get_contents($this->path));
    }

    #[Test]
    public function shouldNotGenerateCorrectAllowAllResource()
    {
        //given
        Route::allowAll('/api', 'Controller\\Api\\UsersController');

        //when
        $generated = JsUriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
function checkParameter(parameter) {
    if (parameter === null) {
        throw new Error("Uri helper: Missing parameters");
    }
}\n
FUNCT;
        $this->assertEquals($expected, $generated);
    }
}