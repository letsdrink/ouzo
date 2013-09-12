<?php
use Ouzo\Config;

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
        $this->assertInstanceOf('\Ouzo\Config', $config);
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
    public function shouldReturnEmptyArrayForMissingSections()
    {
        //given
        $config = Config::load();

        //when
        $section = $config->getConfig('missing');

        //then
        $this->assertEquals(array(), $section);
    }

    /**
     * @test
     */
    public function shouldReadSampleConfig()
    {
        //given
        $config = Config::registerConfig(new SampleConfig);

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
        include_once '/tmp/SampleConfigFile.php';
        $config = Config::registerConfig(new SampleConfigFile);

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
        include_once '/tmp/SampleConfigFile.php';
        Config::registerConfig(new SampleConfigFile);
        $config = Config::registerConfig(new SampleConfig);


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