<?php

namespace Ouzo\Console;

use Ouzo\Injection\Injector;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Utilities\Path;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class CommandsLoaderTest extends TestCase
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

    /**
     * @test
     */
    public function shouldLoadCommandsUsingInjector()
    {
        //given
        $application = Mock::mock(Application::class);
        $injector = Mock::mock(Injector::class);
        Mock::when($injector)->getInstance("\\TestCommand")->thenReturn(new \TestCommand());
        $loader = CommandsLoader::forApplicationAndInjector($application, $injector);

        //when
        $loader->registerCommandsFromPath($this->testCommandsPath);

        //then
        Mock::verify($application)->addCommands([new \TestCommand()]);
        Mock::verify($injector)->getInstance("\\TestCommand");
    }

}
