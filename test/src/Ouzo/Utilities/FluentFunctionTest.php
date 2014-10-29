<?php

namespace Ouzo\Utilities;


use Model\Test\Product;

class FluentFunctionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldChainFunctionCalls()
    {
        //given
        $function = FluentFunctions::extractField('name')
            ->removePrefix('super')
            ->prepend(' extra')
            ->append('! ')
            ->surroundWith("***");

        //when
        $result = Functions::call($function, new Product(array('name' => 'super phone')));

        //then
        $this->assertEquals('*** extra phone! ***', $result);
    }
}
