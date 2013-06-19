<?php
use Thulium\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnSingleton()
    {
        //when
        $config = Config::load();

        //then
        $this->assertInstanceOf('\Thulium\Config', $config);
    }

    /**
     * @test
     */
    public function shouldGetAllConfig()
    {
        //given
        $config = Config::load();

        //when
        $all = $config->getAllConfig();

        //then
        $this->assertArrayHasKey('db', $all);
        $this->assertArrayHasKey('global', $all);
    }
}