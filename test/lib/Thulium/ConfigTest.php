<?php
use Thulium\Config;
use Thulium\Config\CustomConfig;

class SampleConfig
{
    public function getConfig()
    {
        $config['default']['auth'] = 'SampleAuth';
        $config['default']['class'] = 'class';
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

    /**
     * @test
     */
    public function shouldReadSampleConfigFromFile()
    {
        //given
        $this->_createSampleConfigFile();
        $config = Config::registerConfig(new CustomConfig('SampleConfigFile', '/tmp/SampleConfigFile.php'));

        //when
        $value = $config->getConfig('default');
        $this->_deleteSampleConfigFile();

        //then
        $this->assertEquals('SampleAuthFile', $value['auth']);
    }

    /**
     * @test
     */
    public function shouldReadMultipleSampleConfigs()
    {
        //given
        $this->_createSampleConfigFile();
        Config::registerConfig(new CustomConfig('SampleConfigFile', '/tmp/SampleConfigFile.php'));
        $config = Config::registerConfig(new CustomConfig('SampleConfig'));


        //when
        $value = $config->getConfig('default');
        $this->_deleteSampleConfigFile();

        //then
        $this->assertEquals('file', $value['file']);
        $this->assertEquals('class', $value['class']);
    }

    private function _createSampleConfigFile()
    {
        $classTemplate = <<<'TEMPLATE'
<?php
class SampleConfigFile
{
    public function getConfig()
    {
        $config['default']['auth'] = 'SampleAuthFile';
        $config['default']['file'] = 'file';
        return $config;
    }
}
TEMPLATE;
        file_put_contents('/tmp/SampleConfigFile.php', $classTemplate);
    }

    private function _deleteSampleConfigFile()
    {
        unlink('/tmp/SampleConfigFile.php');
    }
}