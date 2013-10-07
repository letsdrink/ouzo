<?php
namespace Ouzo\Uri;

use Ouzo\Routing\Route;
use Ouzo\Utilities\Path;
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
<?php
function showItemUsersPath()
{
    return url(array('string' => "/users/show_item"));
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
function saveUsersPath()
{
    return url(array('string' => "/users/save"));
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
function getDuplicatedUsersPath()
{
    return url(array('string' => "/users/get_duplicated"));
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
<?php
function showUsersPath($id, $call_id)
{
    return url(array('string' => "/users/show/id/$id/call_id/$call_id"));
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
function userItemPath()
{
    return url(array('string' => "/users/show_item"));
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
<?php
function usersPath()
{
    return url(array('string' => "/users"));
}

function freshUserPath()
{
    return url(array('string' => "/users/fresh"));
}

function editUserPath($id)
{
    return url(array('string' => "/users/$id/edit"));
}

function userPath($id)
{
    return url(array('string' => "/users/$id"));
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
        $this->assertTrue(file_exists($path));
        $this->assertEquals($generator->getGeneratedFunctions(), file_get_contents($path));
        unlink($path);
    }
}
