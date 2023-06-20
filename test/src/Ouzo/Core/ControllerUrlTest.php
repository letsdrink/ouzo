<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\ControllerUrl;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ControllerUrlTest extends TestCase
{
    #[Test]
    public function shouldCreateCorrectUrl()
    {
        //given
        $defaults = Config::getValue('global');

        //when
        $url = ControllerUrl::createUrl(['controller' => 'users', 'action' => 'add']);

        //then
        $this->assertEquals($defaults['prefix_system'] . '/users/add', $url);
    }

    #[Test]
    public function shouldCreateCorrectUrlFromString()
    {
        //given
        $defaults = Config::getValue('global');

        //when
        $url = ControllerUrl::createUrl(['string' => '/users/add']);

        //then
        $this->assertEquals($defaults['prefix_system'] . '/users/add', $url);
    }

    #[Test]
    public function shouldCreateCorrectUrlWithExtraParams()
    {
        //given
        $defaults = Config::getValue('global');

        //when
        $url = ControllerUrl::createUrl([
            'controller' => 'users',
            'action' => 'add',
            'extraParams' => ['id' => 5, 'name' => 'john']
        ]);

        //then
        $this->assertEquals($defaults['prefix_system'] . '/users/add/id/5/name/john', $url);
    }
}
