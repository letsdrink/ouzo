<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Console;

use Symfony\Component\Console\Application;

class CommandsLoader
{
    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public static function forApplication(Application $application)
    {
        return new self($application);
    }

    public function registerCommandsFromPath($path, $namespace = "", $pattern = "*.php")
    {
        $commands = [];
        $files = glob($path . DIRECTORY_SEPARATOR . $pattern);
        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            $class = $namespace . '\\' . $className;
            if (class_exists($class)) {
                $commands[] = new $class();
            }
        }
        $this->application->addCommands($commands);
        return $this;
    }
}