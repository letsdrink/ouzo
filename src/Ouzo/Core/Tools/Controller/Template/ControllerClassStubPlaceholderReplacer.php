<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Controller\Template;

use Ouzo\Utilities\Path;
use Ouzo\Utilities\StrSubstitutor;

class ControllerClassStubPlaceholderReplacer
{
    public function __construct(private ControllerGenerator $generator)
    {
    }

    public function content(): string
    {
        $stubContent = file_get_contents(Path::join(__DIR__, 'stubs', 'class.stub'));
        $substitutor = new StrSubstitutor([
            'namespace' => $this->generator->getClassNamespace(),
            'class' => $this->generator->getClassName()
        ]);
        return $substitutor->replace($stubContent);
    }
}
