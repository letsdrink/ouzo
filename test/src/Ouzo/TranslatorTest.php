<?php
use Ouzo\Translator;

class TranslatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldTranslateSimpleKey()
    {
        //given
        $labels = array('key' => 'translation');
        $translator = new Translator($labels);

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
        $labels = array('key' => 'translation %{param1}');
        $translator = new Translator($labels);

        //when
        $translation = $translator->translate('key', array('param1' => 'value1'));

        //then
        $this->assertEquals('translation value1', $translation);
    }

    /**
     * @test
     */
    public function shouldHandleNestedKeys()
    {
        //given
        $labels = array('prefix1' =>
        array('prefix2' =>
        array('key' => 'translation')
        )
        );

        $translator = new Translator($labels);

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
        $labels = array('prefix1' =>
        array('key' => 'translation')
        );

        $translator = new Translator($labels);

        //when
        $translation = $translator->translate('prefix1.prefix2.key');

        //then
        $this->assertEquals('prefix1.prefix2.key', $translation);
    }
}