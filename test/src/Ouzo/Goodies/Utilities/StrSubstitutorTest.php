<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\StrSubstitutor;

class StrSubstitutorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldSubstituteWithValues()
    {
        //given
        $strSubstitutor = new StrSubstitutor(array('NAME' => 'Marek', 'SURNAME' => 'Kowalski'));

        //when
        $substituted = $strSubstitutor->replace('Czesc {{NAME}} {{SURNAME}}');

        //then
        $this->assertEquals('Czesc Marek Kowalski', $substituted);
    }

    /**
     * @test
     */
    public function shouldLeaveMissingPlaceholders()
    {
        //given
        $strSubstitutor = new StrSubstitutor(array());

        //when
        $substituted = $strSubstitutor->replace('Czesc {{NAME}}');

        //then
        $this->assertEquals('Czesc {{NAME}}', $substituted);
    }

    /**
     * @test
     */
    public function shouldReplaceMissingPlaceholdersWithDefault()
    {
        //given
        $strSubstitutor = new StrSubstitutor(array(), 'Unknown');

        //when
        $substituted = $strSubstitutor->replace('Hi {{NAME}}');

        //then
        $this->assertEquals('Hi Unknown', $substituted);
    }

    /**
     * @test
     */
    public function shouldReplaceMissingPlaceholdersWithEmptyDefault()
    {
        //given
        $strSubstitutor = new StrSubstitutor(array(), '');

        //when
        $substituted = $strSubstitutor->replace('Hi {{NAME}}');

        //then
        $this->assertEquals('Hi ', $substituted);
    }

    /**
     * @test
     */
    public function shouldChangeToEmptyString()
    {
        //given
        $strSubstitutor = new StrSubstitutor(array('EMPTY' => ''));

        //when
        $substituted = $strSubstitutor->replace('Czesc {{EMPTY}}');

        //then
        $this->assertEquals('Czesc ', $substituted);
    }
}
