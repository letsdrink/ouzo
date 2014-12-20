<?php
use Ouzo\Config;
use Ouzo\Tests\Assert;
use Ouzo\Utilities\Files;

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
    public static function setUpBeforeClass()
    {
        $_SESSION['config']['debug'] = false;
        $_SESSION['config']['language'] = 'pl';
        $_SESSION['config']['custom'] = 'value';
        $_SESSION['config']['global']['prefix_system'] = '/sample';
    }

    public static function tearDownAfterClass()
    {
        Config::overrideProperty('debug')->with(true);
        Config::overrideProperty('language')->with('en');
        Config::overridePropertyArray(array('global', 'prefix_system'), '');
        if (Files::exists('/tmp/SampleConfigFile.php')) {
            unlink('/tmp/SampleConfigFile.php');
        }
    }

    /**
     * @test
     */
    public function shouldReturnNullForMissingSections()
    {
        //when
        $section = Config::getValue('missing');

        //then
        $this->assertNull($section);
    }

    /**
     * @test
     */
    public function shouldReadSampleConfig()
    {
        //given
        Config::registerConfig(new SampleConfig);

        //when
        $value = Config::getValue('default');

        //then
        $this->assertEquals('SampleAuth', $value['auth']);
    }

    /**
     * @test
     */
    public function shouldReturnConfigValue()
    {
        //given
        Config::registerConfig(new SampleConfig);

        //when
        $value = Config::getValue('default');

        //then
        $this->assertEquals('SampleAuth', $value['auth']);
    }

    /**
     * @test
     */
    public function shouldReturnNestedConfigValue()
    {
        //given
        Config::registerConfig(new SampleConfig);

        //when
        $value = Config::getValue('default', 'auth');

        //then
        $this->assertEquals('SampleAuth', $value);
    }

    /**
     * @test
     */
    public function shouldReadSampleConfigFromFile()
    {
        //given
        $this->_createSampleConfigFile();
        include_once '/tmp/SampleConfigFile.php';
        Config::registerConfig(new SampleConfigFile);

        //when
        $value = Config::getValue('default');

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
        Config::registerConfig(new SampleConfig);


        //when
        $value = Config::getValue('default');

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

    /**
     * @test
     */
    public function shouldOverrideProperty()
    {
        // given
        Config::overrideProperty('key')->with('value');

        // when
        $value = Config::getValue('key');

        // then
        $this->assertEquals('value', $value);
        Config::clearProperty('key'); // cleanup
    }

    /**
     * @test
     */
    public function shouldOverrideMultidimensionalProperty()
    {
        // given
        Config::overrideProperty('key1', 'key2')->with('value');

        // when
        $value = Config::getValue('key1', 'key2');

        // then
        $this->assertEquals('value', $value);
        Config::clearProperty('key1', 'key2'); // cleanup
    }

    /**
     * @test
     */
    public function shouldRevertProperty()
    {
        // given
        Config::overrideProperty('key1', 'key2')->with('first');
        Config::overrideProperty('key1', 'key2')->with('second');

        // when
        Config::revertProperty('key1', 'key2');

        // then
        $value = Config::getValue('key1', 'key2');
        $this->assertEquals('first', $value);

        Config::clearProperty('key1', 'key2'); // cleanup
    }

    /**
     * @test
     */
    public function revertCalledSecondTimeShouldDoNothingMore()
    {
        // given
        Config::overrideProperty('key')->with('first');
        Config::overrideProperty('key')->with('second');
        Config::revertProperty('key');

        // when
        Config::revertProperty('key');

        // then
        $value = Config::getValue('key');
        $this->assertEquals('first', $value);

        Config::clearProperty('key'); // cleanup
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function revertOnNonExistingKeyShouldThrowException()
    {
        Config::revertProperty('key', 'does', 'not', 'exist');
    }

    /**
     * @test
     */
    public function shouldClearProperty()
    {
        // given
        Config::overrideProperty('key_to_clear')->with('value');

        // when
        Config::clearProperty('key_to_clear');

        // then
        $value = Config::getValue('key_to_clear');
        $this->assertEmpty($value);
    }

    /**
     * @test
     */
    public function shouldReturnAllConfigValues()
    {
        // given
        Config::overrideProperty('key')->with('value');

        //when
        $values = Config::all();

        //then
        $this->assertEquals('value', $values['key']);
        Config::clearProperty('key'); // cleanup
    }

    /**
     * @test
     */
    public function shouldOverrideConfigPropertyBySession()
    {
        //when
        $values = Config::all();

        //then
        Assert::thatArray($values)->containsKeyAndValue(array('debug' => false, 'language' => 'pl', 'custom' => 'value'));
        Assert::thatArray($values['global'])->contains('/sample');
    }
}
