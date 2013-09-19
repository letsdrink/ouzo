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
    public function shouldRemovePrefixWhenStringIsEqualToPrefix()
    {
        //given
        $string = 'prefix';

        //when
        $withoutPrefix = Strings::removePrefix($string, 'prefix');

        //then
        $this->assertEquals('', $withoutPrefix);
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

    /**
     * @test
     */
    public function shouldReturnTrueIfStringStartsWithPrefix()
    {
        //given
        $string = 'prefixRest';

        //when
        $result = Strings::startsWith($string, 'prefix');

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldReturnFalseIfStringDoesNotStartWithPrefix()
    {
        //given
        $string = 'prefixRest';

        //when
        $result = Strings::startsWith($string, 'invalid');

        //then
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function shouldReturnTrueIfStringEndsWithPrefix()
    {
        //given
        $string = 'StringSuffix';

        //when
        $result = Strings::endsWith($string, 'Suffix');

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldReturnFalseIfStringDoesNotEndWithPrefix()
    {
        //given
        $string = 'String';

        //when
        $result = Strings::endsWith($string, 'Suffix');

        //then
        $this->assertFalse($result);
    }
}
