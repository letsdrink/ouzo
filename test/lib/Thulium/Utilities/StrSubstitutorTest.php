<?php

use Thulium\Utilities\StrSubstitutor;

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
}
