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
    public function startsWithShouldReturnFalseForEmptyString()
    {
        //when
        $result = Strings::startsWith(null, 'prefix');

        //then
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function startsWithShouldReturnFalseForEmptyPrefix()
    {
        //when
        $result = Strings::startsWith('string', null);

        //then
        $this->assertFalse($result);
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
    public function endsWithShouldReturnFalseForEmptyString()
    {
        //when
        $result = Strings::endsWith(null, 'prefix');

        //then
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function endsWithShouldReturnFalseForEmptyPrefix()
    {
        //when
        $result = Strings::endsWith('string', null);

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
    public function shouldRemovePartOfString()
    {
        //given
        $string = 'winter is coming???!!!';

        //when
        $result = Strings::remove($string, '???');

        //then
        $this->assertEquals('winter is coming!!!', $result);
    }

    /**
     * @test
     */
    public function shouldTableizeSimpleClassName()
    {
        //given
        $class = "Dragon";

        //when
        $table = Strings::tableize($class);

        //then
        $this->assertEquals("dragons", $table);
    }

    /**
     * @test
     */
    public function shouldTableizeMultipartClassName()
    {
        //given
        $class = "BigFoot";

        //when
        $table = Strings::tableize($class);

        //then
        $this->assertEquals("big_feet", $table);
    }

    /**
     * @test
     */
    public function shouldTableizeEmptyString()
    {
        //given
        $class = "";

        //when
        $table = Strings::tableize($class);

        //then
        $this->assertEquals("", $table);
    }

    /**
     * @test
     */
    public function shouldAppendSuffix()
    {
        //given
        $string = 'Daenerys';

        //when
        $stringWithSuffix = Strings::appendSuffix($string, ' Targaryen');

        //then
        $this->assertEquals('Daenerys Targaryen', $stringWithSuffix);
    }

    /**
     * @test
     */
    public function shouldEscapeNewLines()
    {
        //given
        $string = "My name is <strong>Reek</strong> \nit rhymes with leek";

        //when
        $escaped = Strings::escapeNewLines($string);

        //then
        $this->assertEquals("My name is &lt;strong&gt;Reek&lt;/strong&gt; <br />\nit rhymes with leek", $escaped);
    }

    /**
     * @test
     */
    public function shouldReturnTrueForObjectWithTheSameStringRepresentation()
    {
        $this->assertTrue(Strings::equal('123', 123));
        $this->assertTrue(Strings::equal(123, '123'));
    }

    /**
     * @test
     */
    public function shouldReturnFalseForObjectWithDifferentStringRepresentation()
    {
        $this->assertFalse(Strings::equal('0123', 123));
        $this->assertFalse(Strings::equal(123, '0123'));
    }

    /**
     * @test
     */
    public function shouldReturnFalseForNotBlankString()
    {
        $this->assertFalse(Strings::isBlank('0'));
        $this->assertFalse(Strings::isBlank('a '));
    }

    /**
     * @test
     */
    public function shouldReturnTrueForBlankString()
    {
        $this->assertTrue(Strings::isBlank(''));
        $this->assertTrue(Strings::isBlank(' '));
        $this->assertTrue(Strings::isBlank("\t\n\r"));
    }

    /**
     * @test
     */
    public function shouldTestIfStringIsNotBlank()
    {
        $this->assertTrue(Strings::isNotBlank('a '));
        $this->assertFalse(Strings::isNotBlank("\t\n\r"));
    }

    /**
     * @test
     */
    public function shouldAbbreviateString()
    {
        //given
        $string = 'ouzo is great';

        //when
        $abbreviated = Strings::abbreviate($string, 5);

        //then
        $this->assertEquals("ouzo ...", $abbreviated);
    }

    /**
     * @test
     */
    public function shouldNotAbbreviateStringShorterThanLimit()
    {
        //given
        $string = 'ouzo is great';

        //when
        $abbreviated = Strings::abbreviate($string, 13);

        //then
        $this->assertEquals($string, $abbreviated);
    }

    /**
     * @test
     */
    public function shouldConvertEntitiesWithUtfChars()
    {
        //given
        $string = '<strong>someting</strong> with ó';

        //when
        $entities = Strings::htmlEntities($string);

        //then
        $this->assertEquals('&lt;strong&gt;someting&lt;/strong&gt; with ó', $entities);
    }

    /**
     * @test
     */
    public function shouldTrimString()
    {
        //given
        $string = '  sdf ';

        //when
        $result = Strings::trimToNull($string);

        //then
        $this->assertEquals('sdf', $result);
    }

    /**
     * @test
     */
    public function shouldTrimStringToNull()
    {
        //given
        $string = '   ';

        //when
        $result = Strings::trimToNull($string);

        //then
        $this->assertNull($result);
    }
}