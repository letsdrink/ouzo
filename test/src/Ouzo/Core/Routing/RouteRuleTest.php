<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Controller\TestController;
use Ouzo\Routing\RouteRule;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class RouteRuleTest extends TestCase
{
    #[Test]
    public function shouldGetControllerName()
    {
        //given
        $rule = new RouteRule("GET", "/", TestController::class, "index", false);

        //when
        $name = $rule->getControllerName();

        //then
        $this->assertEquals("Test", $name);
    }
}