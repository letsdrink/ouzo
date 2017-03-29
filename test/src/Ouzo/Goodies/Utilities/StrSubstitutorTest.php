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
        $strSubstitutor = new StrSubstitutor(['NAME' => 'Marek', 'SURNAME' => 'Kowalski']);

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
        $strSubstitutor = new StrSubstitutor([]);

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
        $strSubstitutor = new StrSubstitutor([], 'Unknown');

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
        $strSubstitutor = new StrSubstitutor([], '');

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
        $strSubstitutor = new StrSubstitutor(['EMPTY' => '']);

        //when
        $substituted = $strSubstitutor->replace('Czesc {{EMPTY}}');

        //then
        $this->assertEquals('Czesc ', $substituted);
    }

    /**
     * @test
     */
    public function shouldReplaceUnicodeChar()
    {
        //given
        $strSubstitutor = new StrSubstitutor(['ODDZIAŁ' => 'krakowski']);

        //when
        $substituted = $strSubstitutor->replace('Oddział: {{ODDZIAŁ}}');

        //then
        $this->assertEquals('Oddział: krakowski', $substituted);
    }

    /**
     * @test
     */
    public function shouldReplaceWithSpaceInPlaceholderName()
    {
        //given
        $strSubstitutor = new StrSubstitutor(['SOME PLACEHOLDER' => 'new value']);

        //when
        $substituted = $strSubstitutor->replace('Value: {{SOME PLACEHOLDER}}');

        //then
        $this->assertEquals('Value: new value', $substituted);
    }
}
