<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\I18n;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\TestCase;

class I18nTest extends TestCase
{
    protected function setUp(): void
    {
        I18n::reset();
    }

    protected function tearDown(): void
    {
        Config::clearProperty('language');
        I18n::reset();
    }

    /**
     * @test
     */
    public function shouldTranslateValue()
    {
        //given
        Config::overrideProperty('language')->with('pl');

        //when
        $translation = I18n::t('product.description');

        //then
        $this->assertEquals('Opis produktu', $translation);
    }

    /**
     * @test
     */
    public function shouldTranslateWithChoice()
    {
        //when
        $translation = I18n::t('product.quantity', ['count' => '5'], I18n::pluralizeBasedOn(5));

        //then
        $this->assertEquals('5 products', $translation);
    }

    /**
     * @test
     */
    public function shouldTranslateValueInDefaultLanguageWhenNoLanguageWasSet()
    {
        //when
        $translation = I18n::t('product.description');

        //then
        $this->assertEquals('Product description', $translation);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionForNonExistingLanguage()
    {
        //given
        Config::overrideProperty('language')->with('xx');
        $i18n = new I18n();

        //when
        CatchException::when($i18n)->t('product.description');

        //then
        CatchException::assertThat()->isInstanceOf('Exception');
    }

    /**
     * @test
     */
    public function shouldReturnLabel()
    {
        //when
        $allLabels = I18n::labels('timeAgo');

        //then
        $this->assertGreaterThan(1, sizeof($allLabels));
    }

    /**
     * @test
     */
    public function shouldReturnAllLabels()
    {
        //when
        $allLabels = I18n::labels();

        //then
        $this->assertGreaterThan(1, sizeof($allLabels));
    }
}
