<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Assert;
use Ouzo\Tools\Controller\Template\ControllerGenerator;

use PHPUnit\Framework\TestCase;

class ControllerClassStubPlaceholderReplacerTest extends TestCase
{
    /**
     * @test
     */
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
