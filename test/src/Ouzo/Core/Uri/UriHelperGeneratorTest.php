<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Routing\Route;
use Ouzo\Uri\UriHelperGenerator;
use Ouzo\Utilities\Path;
use PHPUnit\Framework\TestCase;

class UriHelperGeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Route::clear();
    }

    /**
     * @test
     */
    public function shouldGenerateUriHelperForGet()
    {
        //given
        Route::get('/users/show_item', 'Controller\\UsersController', 'show_item');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        //
        $expected = <<<'FUNCT'
<?php
class GeneratedUriHelper {
    
    private static function validateParameter($parameter)
    {
        if (!isset($parameter)) {
            throw new \InvalidArgumentException("Missing parameters");
        }
    }
    
    /**
     * @see \Controller\UsersController::show_item()
     */
    public static function showItemUsersPath() 
    {
        return "/users/show_item";
    }
    
    public static function allGeneratedUriNames() 
    {
        return ['showItemUsersPath'];
    }
}

/**
 * @see \Controller\UsersController::show_item()
 */
function showItemUsersPath() 
{
    return GeneratedUriHelper::showItemUsersPath();
}

function allGeneratedUriNames() 
{
    return GeneratedUriHelper::allGeneratedUriNames();
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
        Route::post('/users/save', 'Controller\\UsersController', 'save');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<'FUNCT'
<?php
class GeneratedUriHelper {
    
    private static function validateParameter($parameter)
    {
        if (!isset($parameter)) {
            throw new \InvalidArgumentException("Missing parameters");
        }
    }
    
    /**
     * @see \Controller\UsersController::save()
     */
    public static function saveUsersPath() 
    {
        return "/users/save";
    }
    
    public static function allGeneratedUriNames() 
    {
        return ['saveUsersPath'];
    }
}

/**
 * @see \Controller\UsersController::save()
 */
function saveUsersPath() 
{
    return GeneratedUriHelper::saveUsersPath();
}

function allGeneratedUriNames() 
{
    return GeneratedUriHelper::allGeneratedUriNames();
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
        Route::any('/users/get_duplicated', 'Controller\\UsersController', 'get_duplicated');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<'FUNCT'
<?php
class GeneratedUriHelper {
    
    private static function validateParameter($parameter)
    {
        if (!isset($parameter)) {
            throw new \InvalidArgumentException("Missing parameters");
        }
    }
    
    /**
     * @see \Controller\UsersController::get_duplicated()
     */
    public static function getDuplicatedUsersPath() 
    {
        return "/users/get_duplicated";
    }
    
    public static function allGeneratedUriNames() 
    {
        return ['getDuplicatedUsersPath'];
    }
}

/**
 * @see \Controller\UsersController::get_duplicated()
 */
function getDuplicatedUsersPath() 
{
    return GeneratedUriHelper::getDuplicatedUsersPath();
}

function allGeneratedUriNames() 
{
    return GeneratedUriHelper::allGeneratedUriNames();
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
        Route::get('/users/show/id/:id/call_id/:call_id', 'Controller\\UsersController', 'show');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<'FUNCT'
<?php
class GeneratedUriHelper {
    
    private static function validateParameter($parameter)
    {
        if (!isset($parameter)) {
            throw new \InvalidArgumentException("Missing parameters");
        }
    }
    
    /**
     * @see \Controller\UsersController::show()
     */
    public static function showUsersPath($id, $call_id) 
    {
        GeneratedUriHelper::validateParameter($id);
        GeneratedUriHelper::validateParameter($call_id);
        return "/users/show/id/$id/call_id/$call_id";
    }
    
    public static function allGeneratedUriNames() 
    {
        return ['showUsersPath'];
    }
}

/**
 * @see \Controller\UsersController::show()
 */
function showUsersPath($id, $call_id) 
{
    return GeneratedUriHelper::showUsersPath($id, $call_id);
}

function allGeneratedUriNames() 
{
    return GeneratedUriHelper::allGeneratedUriNames();
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
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<'FUNCT'
<?php
class GeneratedUriHelper {
    
    private static function validateParameter($parameter)
    {
        if (!isset($parameter)) {
            throw new \InvalidArgumentException("Missing parameters");
        }
    }
    
    /**
     * @see \Controller\UsersController::show_item()
     */
    public static function userItemPath() 
    {
        return "/users/show_item";
    }
    
    public static function allGeneratedUriNames() 
    {
        return ['userItemPath'];
    }
}

/**
 * @see \Controller\UsersController::show_item()
 */
function userItemPath() 
{
    return GeneratedUriHelper::userItemPath();
}

function allGeneratedUriNames() 
{
    return GeneratedUriHelper::allGeneratedUriNames();
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
        Route::resource('Controller\\UsersController', 'users');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<'FUNCT'
<?php
class GeneratedUriHelper {
    
    private static function validateParameter($parameter)
    {
        if (!isset($parameter)) {
            throw new \InvalidArgumentException("Missing parameters");
        }
    }
    
    /**
     * @see \Controller\UsersController::index()
     */
    public static function usersPath() 
    {
        return "/users";
    }
    
    /**
     * @see \Controller\UsersController::fresh()
     */
    public static function freshUserPath() 
    {
        return "/users/fresh";
    }
    
    /**
     * @see \Controller\UsersController::edit()
     */
    public static function editUserPath($id) 
    {
        GeneratedUriHelper::validateParameter($id);
        return "/users/$id/edit";
    }
    
    /**
     * @see \Controller\UsersController::show()
     */
    public static function userPath($id) 
    {
        GeneratedUriHelper::validateParameter($id);
        return "/users/$id";
    }
    
    public static function allGeneratedUriNames() 
    {
        return ['usersPath',
'freshUserPath',
'editUserPath',
'userPath'];
    }
}

/**
 * @see \Controller\UsersController::index()
 */
function usersPath() 
{
    return GeneratedUriHelper::usersPath();
}

/**
 * @see \Controller\UsersController::fresh()
 */
function freshUserPath() 
{
    return GeneratedUriHelper::freshUserPath();
}

/**
 * @see \Controller\UsersController::edit()
 */
function editUserPath($id) 
{
    return GeneratedUriHelper::editUserPath($id);
}

/**
 * @see \Controller\UsersController::show()
 */
function userPath($id) 
{
    return GeneratedUriHelper::userPath($id);
}

function allGeneratedUriNames() 
{
    return GeneratedUriHelper::allGeneratedUriNames();
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
        Route::get('/api/users/:id/orders', 'Controller\\Api\\UsersController', 'orders');

        //when
        $generated = UriHelperGenerator::generate()->getGeneratedFunctions();

        //then
        $expected = <<<'FUNCT'
<?php
class GeneratedUriHelper {
    
    private static function validateParameter($parameter)
    {
        if (!isset($parameter)) {
            throw new \InvalidArgumentException("Missing parameters");
        }
    }
    
    /**
     * @see \Controller\Api\UsersController::orders()
     */
    public static function ordersUsersApiPath($id) 
    {
        GeneratedUriHelper::validateParameter($id);
        return "/api/users/$id/orders";
    }
    
    public static function allGeneratedUriNames() 
    {
        return ['ordersUsersApiPath'];
    }
}

/**
 * @see \Controller\Api\UsersController::orders()
 */
function ordersUsersApiPath($id) 
{
    return GeneratedUriHelper::ordersUsersApiPath($id);
}

function allGeneratedUriNames() 
{
    return GeneratedUriHelper::allGeneratedUriNames();
}
FUNCT;
        $this->assertEquals($expected, $generated);
    }
}
