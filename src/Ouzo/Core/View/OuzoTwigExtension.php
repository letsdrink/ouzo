<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\View;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OuzoTwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        $uriHelperFunctions = allGeneratedUriNames();

        $helperFunctions = [
            't', 'linkTo', 'linkButton', 'formButton', 'translatableOptions', 'labelTag',
            'hiddenTag', 'textFieldTag', 'textAreaTag', 'checkboxTag', 'selectTag', 'optionTag', 'passwordFieldTag',
            'radioButtonTag', 'endFormTag', 'formTag', 'formFor', 'showErrors', 'showNotices', 'showSuccess',
            'showWarnings', 'renderPartial',
        ];

        return array_merge(
            $this->toSimpleFunctions($helperFunctions, ['is_safe' => ['html']]),
            $this->toSimpleFunctions($uriHelperFunctions)
        );
    }

    public function getName()
    {
        return 'ouzo';
    }

    private function toSimpleFunctions($functions, array $options = [])
    {
        $result = [];
        foreach ($functions as $function) {
            $result[] = new TwigFunction($function, $function, $options);
        }
        return $result;
    }
}
