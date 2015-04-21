<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\RecursiveStrSubstitutor;

class RecursiveStrSubstitutorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldSubstituteNormalValues()
    {
        //given
        $strSubstitutor = new RecursiveStrSubstitutor(array('NAME' => 'John', 'SURNAME' => 'Smith'));

        //when
        $substituted = $strSubstitutor->replace('Hi {{NAME}} {{SURNAME}}');

        //then
        $this->assertEquals('Hi John Smith', $substituted);
    }

    /**
     * @test
     */
    public function shouldSubstituteRecursively()
    {
        //given
        $strSubstitutor = new RecursiveStrSubstitutor(array('URL' => '{{HOST}}', 'HOST' => 'ouzoframework.org'));

        //when
        $substituted = $strSubstitutor->replace('Best website: {{URL}}');

        //then
        $this->assertEquals('Best website: ouzoframework.org', $substituted);
    }

    /**
     * @test
     */
    public function shouldReturnValueWhenInfinityLoopOccurs()
    {
        //given
        $strSubstitutor = new RecursiveStrSubstitutor(array('URL' => '{{HOST}}', 'HOST' => '{{URL}}'), null, 10);

        //when
        $substituted = $strSubstitutor->replace('Best website: {{URL}}');

        //then
        $this->assertEquals('Best website: {{URL}}', $substituted);
    }
}
