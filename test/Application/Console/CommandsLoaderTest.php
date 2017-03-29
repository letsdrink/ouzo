<?php

namespace Ouzo\Console;

use Ouzo\Tests\Mock\Mock;
use Ouzo\Utilities\Path;
use Symfony\Component\Console\Application;

class CommandsLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $testCommandsPath;

    protected function setUp()
    {
        parent::setUp();
        $this->testCommandsPath = Path::join(ROOT_PATH, "test", "resources", "commands");
        require_once($this->testCommandsPath . DIRECTORY_SEPARATOR . "TestCommand.php");
    }

    /**
     * @test
     */
    public function shouldSearchCommandsInSpecifiedPath()
    {
        //given
        $application = Mock::mock(Application::class);
        $loader = CommandsLoader::forApplication($application);

        //when
        $loader->registerCommandsFromPath($this->testCommandsPath);

        //then
        Mock::verify($application)->addCommands([new \TestCommand()]);
    }

}
