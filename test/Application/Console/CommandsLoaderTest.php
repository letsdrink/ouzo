<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Console;

use Ouzo\Injection\Injector;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use Ouzo\Utilities\Path;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use TestCommand;

class CommandsLoaderTest extends TestCase
{
    private Application|MockInterface $application;
    private Injector|MockInterface $injector;

    private CommandsLoader $commandsLoader;
    private string $testCommandsPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->application = Mock::mock(Application::class);
        $this->injector = Mock::mock(Injector::class);

        Mock::when($this->injector)->getInstance('\TestCommand')->thenReturn(new TestCommand());

        $this->commandsLoader = CommandsLoader::forApplicationAndInjector($this->application, $this->injector);
        $this->testCommandsPath = Path::join(ROOT_PATH, 'test', 'resources', 'commands');
    }

    #[Test]
    public function shouldSearchCommandsInSpecifiedPath()
    {
        //when
        $this->commandsLoader->registerCommandsFromPath($this->testCommandsPath);

        //then
        Mock::verify($this->application)->addCommands([new TestCommand()]);
    }

    #[Test]
    public function shouldLoadCommandsUsingInjector()
    {
        //when
        $this->commandsLoader->registerCommandsFromPath($this->testCommandsPath);

        //then
        Mock::verify($this->application)->addCommands([new TestCommand()]);
        Mock::verify($this->injector)->getInstance('\TestCommand');
    }

    #[Test]
    public function shouldNotLoadAbstractClass()
    {
        //when
        $this->commandsLoader->registerCommandsFromPath($this->testCommandsPath);

        //then
        Mock::verify($this->application)->addCommands([new TestCommand()]);
    }
}
