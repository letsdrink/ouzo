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
    /**
     * @var ControllerGenerator
     */
    private $generator;

    public function __construct(ControllerGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function content()
    {
        $stubContent = file_get_contents($this->stubFilePath());
        $strSubstitutor = new StrSubstitutor(array(
            'namespace' => $this->generator->getClassNamespace(),
            'class' => $this->generator->getClassName()
        ));
        return $strSubstitutor->replace($stubContent);
    }

    private function stubFilePath()
    {
        return Path::join(__DIR__, 'stubs', 'class.stub');
    }
}
