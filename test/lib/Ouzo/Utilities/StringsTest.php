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

    /**
     * @test
     */
    public function shouldCheckEqualityIgnoringCase()
    {
        $this->assertTrue(Strings::equalsIgnoreCase('', ''));
        $this->assertTrue(Strings::equalsIgnoreCase('ABC123', 'ABC123'));
        $this->assertTrue(Strings::equalsIgnoreCase('ABC123', 'abc123'));
        $this->assertFalse(Strings::equalsIgnoreCase('ABC123', 'abc123a'));
        $this->assertFalse(Strings::equalsIgnoreCase('ABC123', 'abc1234'));
        $this->assertFalse(Strings::equalsIgnoreCase('', 'abc123'));
        $this->assertFalse(Strings::equalsIgnoreCase('ABC123', ''));
        $this->assertTrue(Strings::equalsIgnoreCase(null, ''));
        $this->assertTrue(Strings::equalsIgnoreCase('', null));
        $this->assertTrue(Strings::equalsIgnoreCase(null, null));
        $this->assertFalse(Strings::equalsIgnoreCase('null', null));
    }

    /**
     * @test
     */
    public function shouldRemoveString()
    {
        //given
        $string = 'winter is coming???!!!';

        //when
        $result = Strings::removeString($string, '???');

        //then
        $this->assertEquals('winter is coming!!!', $result);
    }
}