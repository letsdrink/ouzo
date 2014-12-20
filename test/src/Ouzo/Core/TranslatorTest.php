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
        $labels = array('key' => 'translation %{param1}');
        $translator = new Translator('en', $labels);

        //when
        $translation = $translator->translate('key', array('param1' => 'value1'));

        //then
        $this->assertEquals('translation value1', $translation);
    }

    /**
     * @test
     */
    public function shouldSupportPluralizationForEnglish()
    {
        //given
        $labels = array('key' => 'I\'ve got %{n} leg|I\'ve got %{n} legs');
        $translator = new Translator('en', $labels);

        //when
        $translation1 = $translator->translateWithChoice('key', 1, array('n' => '1'));
        $translation2 = $translator->translateWithChoice('key', 2, array('n' => '2'));

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
        $labels = array('key' => 'I\'ve got %{n} leg');
        $translator = new Translator('en', $labels);

        //when
        $translation = $translator->translateWithChoice('key', 5, array('n' => '5'));

        //then
        $this->assertEquals("I've got 5 leg", $translation);
    }

    /**
     * @test
     */
    public function shouldSupportPluralizationForPolish()
    {
        //given
        $labels = array('key' => 'Mam %{n} rok|Mam %{n} lata|Mam %{n} lat');
        $translator = new Translator('pl', $labels);

        //when
        $translation1 = $translator->translateWithChoice('key', 1, array('n' => '1'));
        $translation2 = $translator->translateWithChoice('key', 2, array('n' => '2'));
        $translation3 = $translator->translateWithChoice('key', 5, array('n' => '5'));

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
        $labels = array('prefix1' => array('prefix2' => array('key' => 'translation')));
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
        $labels = array('prefix1' => array('key' => 'translation'));
        $translator = new Translator('en', $labels);

        //when
        $translation = $translator->translate('prefix1.prefix2.key');

        //then
        $this->assertEquals('prefix1.prefix2.key', $translation);
    }
}
