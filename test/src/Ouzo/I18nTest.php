<?php
namespace Ouzo;

class I18nTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @tearDown
     */
    protected function tearDown() {
        parent::tearDown();

        Config::clearProperty('language');
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
        $this->assertEquals('Product description', $translation);
    }


}
