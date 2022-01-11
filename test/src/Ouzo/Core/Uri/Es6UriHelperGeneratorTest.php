<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Utilities;

use Ouzo\Config;
use Ouzo\Routing\Route;
use Ouzo\Uri\Es6UriHelperGenerator;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

use PHPUnit\Framework\TestCase;

class Es6UriHelperGeneratorTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        parent::setUp();
        Route::clear();
        $this->path = Path::joinWithTemp(uniqid() . '_generatedUriHelper.js');
        Config::overrideProperty("global", "prefix_system")->with("/app");
        Route::$isDebug = false;
    }

    protected function tearDown(): void
    {
        Config::revertProperty("global", "prefix_system");
        if (Files::exists($this->path)) {
            unlink($this->path);
        }
        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForGet()
    {
        //given
        Route::get('/users/show_item', 'Controller\\UsersController', 'show_item');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<EXPECTED
const checkParameters = (...args) => {
    args.forEach(arg => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}

export const showItemUsersPath = () => '/app/users/show_item'

EXPECTED;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForPost()
    {
        //given
        Route::post('/users/save', 'Controller\\UsersController', 'save');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameters = (...args) => {
    args.forEach(arg => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}

export const saveUsersPath = () => '/app/users/save'

FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForAny()
    {
        //given
        Route::any('/users/get_duplicated', 'Controller\\UsersController', 'get_duplicated');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameters = (...args) => {
    args.forEach(arg => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}

export const getDuplicatedUsersPath = () => '/app/users/get_duplicated'

FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperWhenMultipleBindParameters()
    {
        //given
        Route::get('/users/show/id/:id/call_id/:call_id', 'Controller\\UsersController', 'show');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameters = (...args) => {
    args.forEach(arg => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}

export const showUsersPath = (id, call_id) => {
    checkParameters(id, call_id)
    return '/app/users/show/id/' + id + '/call_id/' + call_id
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
        Route::get('/users/show_item', 'Controller\\UsersController', 'show_item', ['as' => 'user_item']);

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameters = (...args) => {
    args.forEach(arg => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}

export const userItemPath = () => '/app/users/show_item'

FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForResource()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameters = (...args) => {
    args.forEach(arg => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}

export const usersPath = () => '/app/users'

export const freshUserPath = () => '/app/users/fresh'

export const editUserPath = (id) => {
    checkParameters(id)
    return '/app/users/' + id + '/edit'
}

export const userPath = (id) => {
    checkParameters(id)
    return '/app/users/' + id
}

FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateCorrectNestedResources()
    {
        //given
        Route::get('/api/users/:id/orders', 'Controller\\Api\\UsersController', 'orders');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameters = (...args) => {
    args.forEach(arg => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}

export const ordersUsersApiPath = (id) => {
    checkParameters(id)
    return '/app/api/users/' + id + '/orders'
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
        Route::resource('Controller\\UsersController', 'users');
        $generator = Es6UriHelperGenerator::generate();

        //when
        $generator->saveToFile($this->path);

        //then
        $this->assertFileExists($this->path);
        $this->assertEquals($generator->getGeneratedFunctions(), file_get_contents($this->path));
    }

    /**
     * @test
     */
    public function shouldNotGenerateCorrectAllowAllResource()
    {
        //given
        Route::allowAll('/api', 'Controller\\Api\\UsersController');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
const checkParameters = (...args) => {
    args.forEach(arg => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}

FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperWhenMultipleBindParametersTs()
    {
        //given
        Route::get('/users/show/id/:id/call_id/:call_id', 'Controller\\UsersController', 'show');

        //when
        $generated = Es6UriHelperGenerator::generate('ts')->getGeneratedFunctions();

        //then
        $expected = <<<FUNCT
type UriParam = string | number

const checkParameters = (...args: UriParam[]): void => {
    args.forEach((arg: UriParam) => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}

export const showUsersPath = (id: UriParam, call_id: UriParam): string => {
    checkParameters(id, call_id)
    return '/app/users/show/id/' + id + '/call_id/' + call_id
}

FUNCT;
        $this->assertEquals($expected, $generated);
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForFirstDuplicatedEntry()
    {
        //given
        Route::get('/users/show_item_1', 'Controller\\UsersController', 'show_item');
        Route::get('/users/show_item_2', 'Controller\\UsersController', 'show_item');

        //when
        $generated = Es6UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<EXPECTED
const checkParameters = (...args) => {
    args.forEach(arg => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}

export const showItemUsersPath = () => '/app/users/show_item_1'

EXPECTED;
        $this->assertEquals($expected, $generated);
    }
}