<?php

use Ouzo\Utilities\Strings;

class StringsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConvertUnderscoreToCamelCase()
    {
        //given
        $string = 'lannisters_always_pay_their_debts';

        //when
        $camelcase = Strings::underscoreToCamelCase($string);

        //then
        $this->assertEquals('LannistersAlwaysPayTheirDebts', $camelcase);
    }

    /**
     * @test
     */
    public function shouldConvertCamelCaseToUnderscore()
    {
        //given
        $string = 'LannistersAlwaysPayTheirDebts';

        //when
        $underscored = Strings::camelCaseToUnderscore($string);

        //then
        $this->assertEquals('lannisters_always_pay_their_debts', $underscored);
    }

    /**
     * @test
     */
    public function shouldRemovePrefix()
    {
        //given
        $string = 'prefixRest';

        //when
        $withoutPrefix = Strings::removePrefix($string, 'prefix');

        //then
        $this->assertEquals('Rest', $withoutPrefix);
    }

    /**
     * @test
     */
    public function shouldRemovePrefixes()
    {
        //given
        $string = 'prefixRest';

        //when
        $withoutPrefix = Strings::removePrefixes($string, array('pre', 'fix'));

        //then
        $this->assertEquals('Rest', $withoutPrefix);
    }
}
