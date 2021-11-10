<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Console;

use Ouzo\Injection\Injector;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class CommandsLoader
{
    public function __construct(private Application $application, private Injector $injector)
    {
    }

    public static function forApplicationAndInjector(Application $application, Injector $injector = null): static
    {
        return new self($application, $injector);
    }

    public function registerCommandsFromPath(string $path, string $namespace = "", string $pattern = "*.php"): static
    {
        $commands = [];
        $files = glob($path . DIRECTORY_SEPARATOR . $pattern);
        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            $class = $namespace . '\\' . $className;
            if ($this->isValidClass($class)) {
                $commands[] = $this->createInstance($class);
            }
        }
        $this->application->addCommands($commands);
        return $this;
    }

    private function createInstance(string $class): object
    {
        if ($this->injector) {
            return $this->injector->getInstance($class);
        }
        return new $class();
    }

    private function isValidClass(string $class): bool
    {
        try {
            $reflectionClass = new ReflectionClass($class);
            return !$reflectionClass->isAbstract() && $reflectionClass->isSubclassOf(Command::class);
        } catch (ReflectionException) {
            return false;
        }
    }
}
