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
function checkParameter(\$parameter) {
\tif (!isset(\$parameter)) {
\t\tthrow new \InvalidArgumentException("Missing parameters");
\t}
}

function showItemUsersPath()
{
\treturn url("/users/show_item");
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
function checkParameter(\$parameter) {
\tif (!isset(\$parameter)) {
\t\tthrow new \InvalidArgumentException("Missing parameters");
\t}
}

function saveUsersPath()
{
\treturn url("/users/save");
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
function checkParameter(\$parameter) {
\tif (!isset(\$parameter)) {
\t\tthrow new \InvalidArgumentException("Missing parameters");
\t}
}

function getDuplicatedUsersPath()
{
\treturn url("/users/get_duplicated");
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
function checkParameter(\$parameter) {
\tif (!isset(\$parameter)) {
\t\tthrow new \InvalidArgumentException("Missing parameters");
\t}
}

function showUsersPath(\$id, \$call_id)
{
\tcheckParameter(\$id);
\tcheckParameter(\$call_id);
\treturn url("/users/show/id/\$id/call_id/\$call_id");
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
function checkParameter(\$parameter) {
\tif (!isset(\$parameter)) {
\t\tthrow new \InvalidArgumentException("Missing parameters");
\t}
}

function userItemPath()
{
\treturn url("/users/show_item");
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
function checkParameter(\$parameter) {
\tif (!isset(\$parameter)) {
\t\tthrow new \InvalidArgumentException("Missing parameters");
\t}
}

function usersPath()
{
\treturn url("/users");
}

function freshUserPath()
{
\treturn url("/users/fresh");
}

function editUserPath(\$id)
{
\tcheckParameter(\$id);
\treturn url("/users/\$id/edit");
}

function userPath(\$id)
{
\tcheckParameter(\$id);
\treturn url("/users/\$id");
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