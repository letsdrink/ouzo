<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\Assert;
use Ouzo\Tools\Controller\Template\ControllerGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ControllerClassStubPlaceholderReplacerTest extends TestCase
{
    #[Test]
    public function shouldReplaceClassNameAndNamespace()
    {
        //given
        $generator = new ControllerGenerator('users');

        //when
        $templateContents = $generator->templateContents();

        //then
        Assert::thatString($templateContents)
            ->contains('namespace \Application\Controller;')
            ->contains('class UsersController extends Controller');
    }
}
