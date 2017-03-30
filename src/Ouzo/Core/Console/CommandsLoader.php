<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Console;

use Ouzo\Injection\Injector;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class CommandsLoader
{
    /** @var Application */
    private $application;
    /** @var Injector */
    private $injector;

    /**
     * @param Application $application
     * @param Injector|null $injector
     */
    public function __construct(Application $application, Injector $injector = null)
    {
        $this->application = $application;
        $this->injector = $injector;
    }

    /**
     * @param Application $application
     * @return CommandsLoader
     */
    public static function forApplication(Application $application)
    {
        return new self($application);
    }

    /**
     * @param Application $application
     * @param Injector|null $injector
     * @return CommandsLoader
     */
    public static function forApplicationAndInjector(Application $application, Injector $injector = null)
    {
        return new self($application, $injector);
    }

    /**
     * @param string $path
     * @param string $namespace
     * @param string $pattern
     * @return $this
     */
    public function registerCommandsFromPath($path, $namespace = "", $pattern = "*.php")
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

    /**
     * @param string $class
     * @return object
     */
    private function createInstance($class)
    {
        if ($this->injector) {
            return $this->injector->getInstance($class);
        }
        return new $class();
    }

    /**
     * @param $class
     * @return bool
     */
    private function isValidClass($class)
    {
        return class_exists($class) && is_subclass_of($class, Command::class);
    }
}
