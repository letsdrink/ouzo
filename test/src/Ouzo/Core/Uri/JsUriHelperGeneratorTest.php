<?php

namespace Ouzo\Uri;

use Ouzo\Config;

class JsUriHelperGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldGetApplicationHttpPathWhenNoApplicationPrefix()
    {
        //given
        Config::overrideProperty("global", "prefix_system")->with("/root");
        $jsGenerator = new JsUriHelperGenerator();

        //when
        $path = $jsGenerator->applicationHttpPath();

        //then
        $this->assertEquals("/root/", $path);
        Config::revertProperty("global", "prefix_system");
    }

    /**
     * @test
     */
    public function shouldGetApplicationHttpPathWithApplicationPrefix()
    {
        //given
        Config::overrideProperty("global", "prefix_system")->with("/root");
        Config::overrideProperty("global", "prefix_application")->with("application_root");
        $jsGenerator = new JsUriHelperGenerator();

        //when
        $path = $jsGenerator->applicationHttpPath();

        //then
        $this->assertEquals("/root/application_root/", $path);
        Config::revertProperty("global", "prefix_system");
        Config::revertProperty("global", "prefix_application");
    }
}
