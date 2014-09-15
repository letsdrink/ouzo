<?php
namespace Ouzo;

use Ouzo\Tests\CatchException;

class I18nTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @tearDown
     */
    protected function setUp()
    {
        I18n::reset();
    }

    /**
     * @tearDown
     */
    protected function tearDown()
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