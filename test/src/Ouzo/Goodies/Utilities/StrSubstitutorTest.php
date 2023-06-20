<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\StrSubstitutor;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class StrSubstitutorTest extends TestCase
{
    #[Test]
    public function shouldSubstituteWithValues()
    {
        //given
        $strSubstitutor = new StrSubstitutor(['NAME' => 'Marek', 'SURNAME' => 'Kowalski']);

        //when
        $substituted = $strSubstitutor->replace('Czesc {{NAME}} {{SURNAME}}');

        //then
        $this->assertEquals('Czesc Marek Kowalski', $substituted);
    }

    #[Test]
    public function shouldLeaveMissingPlaceholders()
    {
        //given
        $strSubstitutor = new StrSubstitutor([]);

        //when
        $substituted = $strSubstitutor->replace('Czesc {{NAME}}');

        //then
        $this->assertEquals('Czesc {{NAME}}', $substituted);
    }

    #[Test]
    public function shouldReplaceMissingPlaceholdersWithDefault()
    {
        //given
        $strSubstitutor = new StrSubstitutor([], 'Unknown');

        //when
        $substituted = $strSubstitutor->replace('Hi {{NAME}}');

        //then
        $this->assertEquals('Hi Unknown', $substituted);
    }

    #[Test]
    public function shouldReplaceMissingPlaceholdersWithEmptyDefault()
    {
        //given
        $strSubstitutor = new StrSubstitutor([], '');

        //when
        $substituted = $strSubstitutor->replace('Hi {{NAME}}');

        //then
        $this->assertEquals('Hi ', $substituted);
    }

    #[Test]
    public function shouldChangeToEmptyString()
    {
        //given
        $strSubstitutor = new StrSubstitutor(['EMPTY' => '']);

        //when
        $substituted = $strSubstitutor->replace('Czesc {{EMPTY}}');

        //then
        $this->assertEquals('Czesc ', $substituted);
    }

    #[Test]
    public function shouldReplaceUnicodeChar()
    {
        //given
        $strSubstitutor = new StrSubstitutor(['ODDZIAŁ' => 'krakowski']);

        //when
        $substituted = $strSubstitutor->replace('Oddział: {{ODDZIAŁ}}');

        //then
        $this->assertEquals('Oddział: krakowski', $substituted);
    }

    #[Test]
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
