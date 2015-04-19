<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

class Bootstrap
{
    public function __construct()
    {
        error_reporting(E_ALL);
        putenv('environment=prod');
    }

    public function addConfig($config)
    {
        Config::registerConfig($config);
        return $this;
    }

    public function runApplication()
    {
        set_exception_handler('\Ouzo\ExceptionHandling\ErrorHandler::exceptionHandler');
        set_error_handler('\Ouzo\ExceptionHandling\ErrorHandler::errorHandler');
        register_shutdown_function('\Ouzo\ExceptionHandling\ErrorHandler::shutdownHandler');

        $this->_includeRoutes();

        $controller = new FrontController();
        $controller->init();
    }

    private function _includeRoutes()
    {
        $routesPath = Path::join(ROOT_PATH, 'config', 'routes.php');
        Files::loadIfExists($routesPath);
    }
}
