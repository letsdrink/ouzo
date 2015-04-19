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
    /**
     * @var ActionGenerator
     */
    private $actionGenerator;

    public function __construct(ActionGenerator $actionGenerator)
    {
        $this->actionGenerator = $actionGenerator;
    }

    public function content()
    {
        $stubContent = file_get_contents($this->stubFilePath());
        $strSubstitutor = new StrSubstitutor(array(
            'action' => $this->actionGenerator->getActionName()
        ));
        return $strSubstitutor->replace($stubContent);
    }

    private function stubFilePath()
    {
        return Path::join(__DIR__, 'stubs', 'action.stub');
    }
}
