<?php

use Ouzo\Config;
use Ouzo\Controller;

class SimpleTestController extends Controller
{
}


class ControllerTest extends PHPUnit_Framework_TestCase
{
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

}