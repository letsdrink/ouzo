<?php
use Thulium\Config;
use Thulium\Config\ConfigGetter;
use Thulium\Config\CustomConfig;

class SampleConfig implements ConfigGetter
{
    public function getConfig()
    {
        $config['default']['auth'] = 'SampleAuth';
        return $config;
    }
}

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

    /**
     * @test
     */
    public function shouldReadSampleConfig()
    {
        //given
        $config = Config::registerConfig(new CustomConfig('SampleConfig'));

        //when
        $value = $config->getConfig('default');

        //then
        $this->assertEquals('SampleAuth', $value['auth']);
    }
}