<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\Assert;
use Ouzo\Tools\Controller\Template\ActionGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ActionGeneratorTest extends TestCase
{
    #[Test]
    public function shouldReturnReplacedTemplateContents()
    {
        //given
        $actionGenerator = new ActionGenerator('index');

        //when
        $templateContents = $actionGenerator->templateContents();

        //then
        Assert::thatString($templateContents)
            ->contains('public function index');
    }
}
