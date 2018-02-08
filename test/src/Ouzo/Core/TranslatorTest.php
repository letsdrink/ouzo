<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\Translator;
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldTranslateSimpleKey()
    {
        //given
        $labels = ['key' => 'translation'];
        $translator = new Translator('en', $labels);

        //when
        $translation = $translator->translate('key');

        //then
        $this->assertEquals('translation', $translation);
    }

    /**
     * @test
     */
    public function shouldSubstituteParams()
    {
        //given
        $labels = ['key' => 'translation %{param1}'];
        $translator = new Translator('en', $labels);

        //when
        $translation = $translator->translate('key', ['param1' => 'value1']);

        //then
        $this->assertEquals('translation value1', $translation);
    }

    /**
     * @test
     */
    public function shouldSupportPluralizationForEnglish()
    {
        //given
        $labels = ['key' => 'I\'ve got %{n} leg|I\'ve got %{n} legs'];
        $translator = new Translator('en', $labels);

        //when
        $translation1 = $translator->translateWithChoice('key', 1, ['n' => '1']);
        $translation2 = $translator->translateWithChoice('key', 2, ['n' => '2']);

        //then
        $this->assertEquals("I've got 1 leg", $translation1);
        $this->assertEquals("I've got 2 legs", $translation2);
    }

    /**
     * @test
     */
    public function shouldSupportPluralizationAndReturnLastPossibleEntryWhenProperWasNotFound()
    {
        //given
        $labels = ['key' => 'I\'ve got %{n} leg'];
        $translator = new Translator('en', $labels);

        //when
        $translation = $translator->translateWithChoice('key', 5, ['n' => '5']);

        //then
        $this->assertEquals("I've got 5 leg", $translation);
    }

    /**
     * @test
     */
    public function shouldSupportPluralizationForPolish()
    {
        //given
        $labels = ['key' => 'Mam %{n} rok|Mam %{n} lata|Mam %{n} lat'];
        $translator = new Translator('pl', $labels);

        //when
        $translation1 = $translator->translateWithChoice('key', 1, ['n' => '1']);
        $translation2 = $translator->translateWithChoice('key', 2, ['n' => '2']);
        $translation3 = $translator->translateWithChoice('key', 5, ['n' => '5']);

        //then
        $this->assertEquals("Mam 1 rok", $translation1);
        $this->assertEquals("Mam 2 lata", $translation2);
        $this->assertEquals("Mam 5 lat", $translation3);
    }

    /**
     * @test
     */
    public function shouldHandleNestedKeys()
    {
        //given
        $labels = ['prefix1' => ['prefix2' => ['key' => 'translation']]];
        $translator = new Translator('en', $labels);

        //when
        $translation = $translator->translate('prefix1.prefix2.key');

        //then
        $this->assertEquals('translation', $translation);
    }

    /**
     * @test
     */
    public function shouldReturnKeyIfTranslationNotFound()
    {
        //given
        $labels = ['prefix1' => ['key' => 'translation']];
        $translator = new Translator('en', $labels);

        //when
        $translation = $translator->translate('prefix1.prefix2.key');

        //then
        $this->assertEquals('prefix1.prefix2.key', $translation);
    }

    /**
     * @test
     */
    public function shouldTranslateWithPseudoLocalization()
    {
        //given
        Config::overrideProperty('pseudo_localization')->with(true);

        $labels = ['key' => 'translation'];
        $translator = new Translator('en', $labels);

        //when
        $translation = $translator->translate('key');

        //then
        $this->assertEquals('ŧřȧƞşŀȧŧīǿƞ', $translation);

        Config::clearProperty('pseudo_localization');
    }

    /**
     * @test
     */
    public function shouldTranslateArrayWithPseudoLocalization()
    {
        //given
        Config::overrideProperty('pseudo_localization')->with(true);

        $labels = ['key' => ['k1' => 'value', 'k2' => 'other']];
        $translator = new Translator('en', $labels);

        //when
        $translation = $translator->translate('key');

        //then
        $this->assertEquals(['k1' => 'ṽȧŀŭḗ', 'k2' => 'ǿŧħḗř'], $translation);
        Config::clearProperty('pseudo_localization');
    }
}
