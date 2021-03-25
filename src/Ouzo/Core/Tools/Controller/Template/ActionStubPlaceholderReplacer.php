<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Controller\Template;

use Ouzo\Utilities\Path;
use Ouzo\Utilities\StrSubstitutor;

class ActionStubPlaceholderReplacer
{
    public function __construct(private ActionGenerator $actionGenerator)
    {
    }

    public function content(): string
    {
        $stubContent = file_get_contents(Path::join(__DIR__, 'stubs', 'action.stub'));
        $substitutor = new StrSubstitutor([
            'action' => $this->actionGenerator->getActionName()
        ]);
        return $substitutor->replace($stubContent);
    }
}
