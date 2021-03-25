<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Strings;
use PHPUnit\Framework\TestCase;

class StringsTest extends TestCase
{
    /**
     * @test
     * @dataProvider underscoreToCamelCase
     */
    public function shouldConvertUnderscoreToCamelCase($string, $expected)
    {
        //when
        $camelcase = Strings::underscoreToCamelCase($string);

        //then
        $this->assertSame($expected, $camelcase);
    }

    public function underscoreToCamelCase(): array
    {
        return [
            ['lannisters_always_pay_their_debts', 'LannistersAlwaysPayTheirDebts'],
            ['lannistersAlways_pay_their_debts', 'LannistersAlwaysPayTheirDebts'],
            [null, null],
        ];
    }

    /**
     * @test
     * @dataProvider camelCaseToUnderscore
     */
    public function shouldConvertCamelCaseToUnderscore($string, $expected)
    {
        //when
        $underscored = Strings::camelCaseToUnderscore($string);

        //then
        $this->assertSame($expected, $underscored);
    }

    public function camelCaseToUnderscore(): array
    {
        return [
            ['LannistersĄlwaysPayTheirDebtsĘlephant', 'lannisters_ąlways_pay_their_debts_ęlephant'],
            ['LannistersAlwaysPay_Their_Debts', 'lannisters_always_pay_their_debts'],
            ['SomeComplicatedRfc222Name', 'some_complicated_rfc222_name'],
            ['WhatIsOAuth', 'what_is_o_auth'],
            [null, null],
        ];
    }

    /**
     * @test
     * @dataProvider removePrefix
     */
    public function shouldRemovePrefix($string, $prefix, $expected)
    {
        //when
        $withoutPrefix = Strings::removePrefix($string, $prefix);

        //then
        $this->assertSame($expected, $withoutPrefix);
    }

    public function removePrefix(): array
    {
        return [
            ['prefixRest', 'prefix', 'Rest'],
            ['prefix', 'prefix', ''],
            ['prefixRest', null, 'prefixRest'],
            [null, 'prefix', null],
        ];
    }

    /**
     * @test
     * @dataProvider removePrefixes
     */
    public function shouldRemovePrefixes($string, $prefixes, $expected)
    {
        //when
        $withoutPrefix = Strings::removePrefixes($string, $prefixes);

        //then
        $this->assertSame($expected, $withoutPrefix);
    }

    public function removePrefixes(): array
    {
        return [
            ['prefixRest', ['pre', 'fix'], 'Rest'],
            [null, ['pre', 'fix'], null],
            ['prefixRest', [], 'prefixRest'],
            ['prefixRest', null, 'prefixRest'],
        ];
    }

    /**
     * @test
     * @dataProvider removeSuffix
     */
    public function shouldRemoveSuffix($string, $suffix, $expected)
    {
        //when
        $withoutSuffix = Strings::removeSuffix($string, $suffix);

        //then
        $this->assertSame($expected, $withoutSuffix);
    }

    public function removeSuffix(): array
    {
        return [
            ['JohnSnow', 'Snow', 'John'],
            ['JohnSnow', null, 'JohnSnow'],
            [null, 'Snow', null],
        ];
    }

    /**
     * @test
     * @dataProvider startsWith
     */
    public function shouldReturnTrueIfStringStartsWithPrefix($string, $prefix, $expected)
    {
        //when
        $result = Strings::startsWith($string, $prefix);

        //then
        $this->assertSame($expected, $result);
    }

    public function startsWith(): array
    {
        return [
            ['prefixRest', 'prefix', true],
            ['48123', 48, true],
            ['48123', '', true],
            [48123, 48, true],

            [null, 'prefix', false],
            ['prefixRest', null, false],
            ['prefixRest', 'invalid', false],
        ];
    }

    /**
     * @test
     * @dataProvider endsWith
     */
    public function shouldReturnTrueIfStringEndsWithPrefix($string, $suffix, $expected)
    {
        //when
        $result = Strings::endsWith($string, $suffix);

        //then
        $this->assertSame($expected, $result);
    }

    public function endsWith(): array
    {
        return [
            ['StringSuffix', 'Suffix', true],
            ['1231', 31, true],
            ['48123', '', true],
            [1231, 31, true],

            ['String', 'Suffix', false],
            ['String', 'invalid', false],
            [null, 'Suffix', false],
            ['string', null, false],
            ['48123', null, false],
        ];
    }

    /**
     * @test
     * @dataProvider equalsIgnoreCase
     */
    public function shouldCheckEqualityIgnoringCase($string1, $string2, $expected)
    {
        //when
        $result = Strings::equalsIgnoreCase($string1, $string2);

        //then
        $this->assertSame($expected, $result);
    }

    public function equalsIgnoreCase(): array
    {
        return [
            ['', '', true],
            ['ABC123', 'ABC123', true],
            ['ABC123', 'abc123', true],
            [null, null, true],

            [null, '', false],
            ['', null, false],
            ['null', null, false],
            ['ABC123', 'abc123a', false],
            ['ABC123', 'abc1234', false],
            ['', 'abc123', false],
            ['ABC123', '', false],
        ];
    }

    /**
     * @test
     * @dataProvider remove
     */
    public function shouldRemovePartOfString($string, $toRemove, $expected)
    {
        //when
        $result = Strings::remove($string, $toRemove);

        //then
        $this->assertSame($expected, $result);
    }

    public function remove(): array
    {
        return [
            ['winter is coming???!!!', '???', 'winter is coming!!!'],
            [null, '???', null],
            ['winter is coming???!!!', null, 'winter is coming???!!!'],
        ];
    }

    /**
     * @test
     * @dataProvider appendSuffix
     */
    public function shouldAppendSuffix($string, $suffix, $expected)
    {
        //when
        $stringWithSuffix = Strings::appendSuffix($string, $suffix);

        //then
        $this->assertSame($expected, $stringWithSuffix);
    }

    public function appendSuffix(): array
    {
        return [
            ['Daenerys', ' Targaryen', 'Daenerys Targaryen'],
            [null, ' Targaryen', null],
            ['Daenerys', null, 'Daenerys'],
        ];
    }

    /**
     * @test
     * @dataProvider appendPrefix
     */
    public function shouldAppendPrefix($string, $prefix, $expected)
    {
        //when
        $stringWithSuffix = Strings::appendPrefix($string, $prefix);

        //then
        $this->assertSame($expected, $stringWithSuffix);
    }

    public function appendPrefix(): array
    {
        return [
            ['Targaryen', 'Daenerys ', 'Daenerys Targaryen'],
            [null, ' Daenerys ', null],
            ['Targaryen', null, 'Targaryen'],
        ];
    }

    /**
     * @test
     * @dataProvider appendIfMissing
     */
    public function shouldAppendSuffixIfNecessary($string, $suffix, $expected)
    {
        // when
        $result = Strings::appendIfMissing($string, $suffix);

        // then
        $this->assertSame($expected, $result);
    }

    public function appendIfMissing(): array
    {
        return [
            ['You know nothing, Jon Snow', ', Jon Snow', 'You know nothing, Jon Snow'],
            ['You know nothing', ', Jon Snow', 'You know nothing, Jon Snow'],
            [null, ', Jon Snow', null],
            ['You know nothing', null, 'You know nothing'],
        ];
    }

    /**
     * @test
     * @dataProvider prependIfMissing
     */
    public function shouldAppendPrefixIfNecessary($string, $prefix, $expected)
    {
        // when
        $original = Strings::prependIfMissing($string, $prefix);

        // then
        $this->assertSame($expected, $original);
    }

    public function prependIfMissing(): array
    {
        return [
            ['Khal Drogo', 'Khal ', 'Khal Drogo'],
            ['Drogo', 'Khal ', 'Khal Drogo'],
            [null, 'Khal ', null],
            ['Drogo', null, 'Drogo'],
        ];
    }

    /**
     * @test
     * @dataProvider tableize
     */
    public function shouldTableizeSimpleClassName($class, $expected)
    {
        //when
        $table = Strings::tableize($class);

        //then
        $this->assertSame($expected, $table);
    }

    public function tableize(): array
    {
        return [
            ['Dragon', 'dragons'],
            ['BigFoot', 'big_feet'],
            ['', ''],
            [null, null],
        ];
    }

    /**
     * @test
     * @dataProvider escapeNewLines
     */
    public function shouldEscapeNewLines($string, $expected)
    {
        //when
        $escaped = Strings::escapeNewLines($string);

        //then
        $this->assertSame($expected, $escaped);
    }

    public function escapeNewLines(): array
    {
        return [
            ["My name is <strong>Reek</strong> \nit rhymes with leek", "My name is &lt;strong&gt;Reek&lt;/strong&gt; <br />\nit rhymes with leek"],
            ['', ''],
            [null, null],
        ];
    }

    /**
     * @test
     * @dataProvider htmlEntities
     */
    public function shouldConvertEntitiesWithUtfChars($string, $expected)
    {
        //when
        $entities = Strings::htmlEntities($string);

        //then
        $this->assertSame($expected, $entities);
    }

    public function htmlEntities(): array
    {
        return [
            ['<strong>someting</strong> with ó', '&lt;strong&gt;someting&lt;/strong&gt; with ó'],
            ['', ''],
            [null, null],
        ];
    }

    /**
     * @test
     * @dataProvider equal
     */
    public function shouldReturnTrueForObjectWithTheSameStringRepresentation($object1, $object2, $expected)
    {
        //when
        $result = Strings::equal($object1, $object2);

        //then
        $this->assertSame($expected, $result);
    }

    public function equal(): array
    {
        return [
            ['123', 123, true],
            [123, '123', true],
            [null, null, true],

            ['0123', 123, false],
            [123, '0123', false],
            [null, '0123', false],
            ['0123', null, false],
        ];
    }

    /**
     * @test
     * @dataProvider isBlank
     */
    public function shouldReturnFalseForNotBlankString($string, $expected)
    {
        //when
        $result = Strings::isBlank($string);

        //then
        $this->assertSame($expected, $result);
    }

    public function isBlank(): array
    {
        return [
            ['0', false],
            ['a ', false],

            [null, true],
            ['', true],
            [' ', true],
            ["\n\r\t", true],
        ];
    }

    /**
     * @test
     * @dataProvider isNotBlank
     */
    public function shouldTestIfStringIsNotBlank($string, $expected)
    {
        //when
        $result = Strings::isNotBlank($string);

        //then
        $this->assertSame($expected, $result);
    }

    public function isNotBlank(): array
    {
        return [
            ['0', true],
            ['a ', true],

            [null, false],
            ['', false],
            [' ', false],
            ["\n\r\t", false],
        ];
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

    /**
     * @test
     */
    public function shouldTrimNull()
    {
        //given
        $string = null;

        //when
        $result = Strings::trimToNull($string);

        //then
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldSprintfStringWithAssocArrayAsParam()
    {
        //given
        $sprintfString = "This is %{what}! %{what}? This is %{place}!";
        $assocArray = [
            'what' => 'madness',
            'place' => 'Sparta'
        ];

        //when
        $resultString = Strings::sprintAssoc($sprintfString, $assocArray);

        //then
        $this->assertEquals('This is madness! madness? This is Sparta!', $resultString);
    }

    /**
     * @test
     */
    public function shouldSprintfStringAndReplaceWithEmptyIfNoPlaceholderFound()
    {
        //given
        $sprintfString = "This is %{what}! This is %{place}! No, this is invalid %{invalid_placeholder} placeholder!";
        $assocArray = [
            'what' => 'madness',
            'place' => 'Sparta'
        ];

        //when
        $resultString = Strings::sprintAssocDefault($sprintfString, $assocArray);

        //then
        $this->assertEquals('This is madness! This is Sparta! No, this is invalid  placeholder!', $resultString);
    }

    /**
     * @test
     */
    public function shouldCheckStringContainsSubstring()
    {
        //given
        $string = 'Fear cuts deeper than swords';

        //when
        $contains = Strings::contains($string, 'deeper');

        //then
        $this->assertTrue($contains);
    }

    /**
     * @test
     */
    public function shouldCheckStringContainsSubstringWhenCaseDoesNotMatch()
    {
        //given
        $string = 'Fear cuts deeper than swords';

        //when
        $contains = Strings::contains($string, 'DeEpEr');

        //then
        $this->assertFalse($contains);
    }

    /**
     * @test
     */
    public function shouldCheckStringContainsSubstringIgnoringCase()
    {
        //given
        $string = 'Fear cuts deeper than swords';

        //when
        $contains = Strings::containsIgnoreCase($string, 'DeEpEr');

        //then
        $this->assertTrue($contains);
    }

    /**
     * @test
     */
    public function shouldGetSubstringBeforeSeparator()
    {
        //given
        $string = 'winter is coming???!!!';

        //when
        $result = Strings::substringBefore($string, '?');

        //then
        $this->assertEquals('winter is coming', $result);
    }

    /**
     * @test
     */
    public function shouldReturnStringIfSeparatorNotFound()
    {
        //given
        $string = 'winter is coming';

        //when
        $result = Strings::substringBefore($string, ',');

        //then
        $this->assertEquals('winter is coming', $result);
    }

    /**
     * @test
     */
    public function shouldGetSubstringAfterSeparator()
    {
        //given
        $string = 'abc+efg+hij';

        //when
        $result = Strings::substringAfter($string, '+');

        //then
        $this->assertEquals('efg+hij', $result);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringInSubstringAfterSeparatorWhenSeparatorIsAtTheEnd()
    {
        //given
        $string = 'abc+';

        //when
        $result = Strings::substringAfter($string, '+');

        //then
        $this->assertEquals('', $result);
    }

    /**
     * @test
     */
    public function shouldReturnStringInSubstringAfterSeparatorWhenSeparatorIsNotFound()
    {
        //given
        $string = 'abc';

        //when
        $result = Strings::substringAfter($string, '-');

        //then
        $this->assertEquals('abc', $result);
    }

    /**
     * @test
     */
    public function shouldReturnNullForNull()
    {
        //when
        $result = Strings::substringBefore(null, ',');

        //then
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldReplaceNthString()
    {
        //given
        $subject = 'name = ? AND description =    ?';

        //when
        $replaceNth = Strings::replaceNth($subject, '\\=\\s*\\?', 'IS NULL', 1);

        //then
        $this->assertEquals('name = ? AND description IS NULL', $replaceNth);
    }

    /**
     * @test
     */
    public function shouldReturnInputStringReplaceNthStringWhenNoSearchFound()
    {
        //given
        $subject = 'name = ? AND description =    ?';

        //when
        $replaceNth = Strings::replaceNth($subject, 'not there', 'IS NULL', 1);

        //then
        $this->assertEquals($subject, $replaceNth);
    }

    /**
     * @test
     */
    public function shouldRemoveAccents()
    {
        //given
        $string = 'String with śżźćółŹĘ ÀÁÂ';

        //when
        $removeAccent = Strings::removeAccent($string);

        //then
        $this->assertEquals('String with szzcolZE AAA', $removeAccent);
    }

    /**
     * @test
     */
    public function shouldUpperCaseFirstLetter()
    {
        //given
        $string = "łukasz";

        //when
        $uppercaseFirst = Strings::uppercaseFirst($string);

        //then
        $this->assertEquals('Łukasz', $uppercaseFirst);
    }

    /**
     * @test
     */
    public function shouldPreserveCaseInRestOfString()
    {
        //given
        $string = "łuKaSz";

        //when
        $uppercaseFirst = Strings::uppercaseFirst($string);

        //then
        $this->assertEquals('ŁuKaSz', $uppercaseFirst);
    }
}
