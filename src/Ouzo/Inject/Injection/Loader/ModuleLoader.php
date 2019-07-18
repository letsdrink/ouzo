<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Loader;

use Ouzo\Injection\InjectorConfig;

class ModuleLoader
{
    /** @var InjectorConfig */
    private $injectorConfig;

    /**
     * @param InjectorConfig $injectorConfig
     */
    public function __construct($injectorConfig)
    {
        $this->injectorConfig = $injectorConfig;
    }

    /**
     * @param InjectModule[] $args
     */
    public function load(...$args)
    {
        foreach ($args as $nameClass) {
            /** @var InjectModule $injectModule */
            $injectModule = new $nameClass;
            $injectModule->configureBindings($this->injectorConfig);
        }
    }
}