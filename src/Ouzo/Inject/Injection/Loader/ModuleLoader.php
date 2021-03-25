<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Loader;

use Ouzo\Injection\InjectorConfig;
use RuntimeException;

class ModuleLoader
{
    public function __construct(private InjectorConfig $injectorConfig)
    {
    }

    public function load(string...$injectModules): void
    {
        foreach ($injectModules as $injectModule) {
            $injectModuleInstance = new $injectModule();

            if (!$injectModuleInstance instanceof InjectModule) {
                throw new RuntimeException("Module '{$injectModule}' is not type of InjectModule.");
            }

            $injectModuleInstance->configureBindings($this->injectorConfig);
        }
    }
}
