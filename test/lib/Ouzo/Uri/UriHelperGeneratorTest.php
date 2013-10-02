<?php
namespace Ouzo\Uri;

use Ouzo\Routing\Route;
use PHPUnit_Framework_TestCase;

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
function showItemUsersPath()
{
    return url(array('string' => '/users/show_item'));
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
function saveUsersPath()
{
    return url(array('string' => '/users/save'));
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
function getDuplicatedUsersPath()
{
    return url(array('string' => '/users/get_duplicated'));
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
        $expected = <<<'FUNCT'
function showUsersPath($id, $call_id)
{
    return url(array('string' => '/users/show/id/$id/call_id/$call_id'));
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
function userItemPath()
{
    return url(array('string' => '/users/show_item'));
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
        $expected = <<<'FUNCT'
function indexUsersPath()
{
    return url(array('string' => '/users'));
}

function freshUsersPath()
{
    return url(array('string' => '/users/fresh'));
}

function editUsersPath($id)
{
    return url(array('string' => '/users/$id/edit'));
}

function showUsersPath($id)
{
    return url(array('string' => '/users/$id'));
}

function createUsersPath()
{
    return url(array('string' => '/users'));
}

function updateUsersPath($id)
{
    return url(array('string' => '/users/$id'));
}

function updateUsersPath($id)
{
    return url(array('string' => '/users/$id'));
}

function destroyUsersPath($id)
{
    return url(array('string' => '/users/$id'));
}
FUNCT;
        $this->assertEquals($expected, $generated);
    }
}