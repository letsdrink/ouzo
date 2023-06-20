<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Product;
use Ouzo\Utilities\FluentFunctions;
use Ouzo\Utilities\Functions;
use PHPUnit\Framework\TestCase;

class FluentFunctionTest extends TestCase
{
    #[Test]
    public function shouldChainFunctionCalls()
    {
        //given
        $function = FluentFunctions::extractField('name')
            ->removePrefix('super')
            ->prepend(' extra')
            ->append('! ')
            ->surroundWith("***");

        //when
        $result = Functions::call($function, new Product(['name' => 'super phone']));

        //then
        $this->assertEquals('*** extra phone! ***', $result);
    }

    #[Test]
    public function shouldNegate()
    {
        //given
        $function = FluentFunctions::startsWith("start")->negate();

        //when
        $result = Functions::call($function, "starts with prefix");

        //then
        $this->assertFalse($result);
    }
}
