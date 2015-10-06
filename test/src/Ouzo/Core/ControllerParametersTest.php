<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\ControllerParameters;

class ControllerParametersTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldRetrieveParameterAsArray()
    {
        $params = new ControllerParameters(array('a' => 'A'), array(), array('b' => 'B'), array('b' => '!', 'c' => 'C'));
        $this->assertEquals('A', $params['a']);
        $this->assertEquals('!', $params['b']);
        $this->assertEquals('C', $params['c']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @test
     */
    public function retrieveShouldThrowExceptionWhenElementWasNotFound()
    {
        $params = new ControllerParameters();
        $params['unknown'];
    }

    /**
     * @expectedException \BadMethodCallException
     * @test
     */
    public function unsetShouldThrowExceptionAsItIsUnsupported()
    {
        $params = new ControllerParameters(array('a' => 'A'));
        unset($params['a']);
    }

    /**
     * @expectedException \BadMethodCallException
     * @test
     */
    public function setShouldThrowExceptionAsItIsUnsupported()
    {
        $params = new ControllerParameters();
        $params['a'] = 'A';
    }

    /**
     * @test
     */
    public function shouldReturnArray()
    {
        // given
        $params = new ControllerParameters(array('a' => 'A'), array(), array('b' => 'B'), array('b' => '!', 'c' => 'C'));

        // when
        $result = $params->toArray();

        // then
        $this->assertEquals(array('a' => 'A', 'b' => '!', 'c' => 'C'), $result);
    }

    /**
     * @test
     */
    public function shouldGetParameterByKey()
    {
        // given
        $params = new ControllerParameters(array('a' => 'A'), array(), array('b' => 'B'), array('b' => '!', 'c' => 'C'));

        // when
        $result = $params->getValue('c');

        // then
        $this->assertEquals('C', $result);
    }

    /**
     * @test
     */
    public function shouldReturnArraysByMagicGetters()
    {
        $params = new ControllerParameters(array('a' => 'A'), array('b' => 'B'), array('c' => 'C'), array('d' => 'D'));
        $this->assertEquals('A', $params->route['a']);
        $this->assertEquals('B', $params->post['b']);
        $this->assertEquals('C', $params->get['c']);
        $this->assertEquals('D', $params->request['d']);
    }
}
