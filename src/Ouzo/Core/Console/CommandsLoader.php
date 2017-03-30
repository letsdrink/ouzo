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
    /**
     * @var Application
     */
    private $application;
    /**
     * @var Injector
     */
    private $injector;

    public function __construct(Application $application, Injector $injector = null)
    {
        $this->application = $application;
        $this->injector = $injector;
    }

    public static function forApplication(Application $application)
    {
        return new self($application);
    }

    public static function forApplicationAndInjector(Application $application, Injector $injector = null)
    {
        return new self($application, $injector);
    }

    public function registerCommandsFromPath($path, $namespace = "", $pattern = "*.php")
    {
        $commands = [];
        $files = glob($path . DIRECTORY_SEPARATOR . $pattern);
        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            $class = $namespace . '\\' . $className;
            if (class_exists($class)) {
                $instance = $this->createInstance($class);
                if ($instance instanceof Command) {
                    $commands[] = $instance;
                }
            }
        }
        $this->application->addCommands($commands);
        return $this;
    }

    private function createInstance($class)
    {
        if ($this->injector) {
            return $this->injector->getInstance($class);
        }
        return new $class();
    }
}