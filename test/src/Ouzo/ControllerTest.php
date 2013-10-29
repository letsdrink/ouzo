<?php
use Ouzo\Controller;
use Ouzo\ControllerFactory;
use Ouzo\Routing\Route;
use Ouzo\Tests\ControllerTestCase;

class SimpleTestController extends Controller
{
    public function download()
    {
        $this->downloadFile('file.txt', 'text/plain', '/tmp/file.txt');
    }
}

class ControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_frontController->controllerFactory = new ControllerFactory('\\');
        Route::$routes = array();
    }

    /**
     * @test
     */
    public function shouldReturnClassNameInUnderscoreAsDefaultTab()
    {
        //when
        $tab = SimpleTestController::getTab();

        //then
        $this->assertEquals('simple_test', $tab);
    }

    /**
     * @test
     * @covers \Ouzo\DownloadHandler
     */
    public function shouldDownloadFile()
    {
        //given
        Route::get('/simple_test/download', 'simple_test#download');

        //when
        $this->get('/simple_test/download');

        //then
        $this->assertDownloadFile('file.txt');
    }
}