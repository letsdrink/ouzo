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
     * @dataProvider abbreviate
     */
    public function shouldAbbreviateString($string, $maxWidth, $expected)
    {
        //when
        $abbreviated = Strings::abbreviate($string, $maxWidth);

        //then
        $this->assertSame($expected, $abbreviated);
    }

    public function abbreviate(): array
    {
        return [
            ['ouzo is great', 5, 'ouzo ...'],
            ['ouzo is great', 13, 'ouzo is great'],
            [null, 5, null],
        ];
    }

    /**
     * @test
     * @dataProvider trimToNull
     */
    public function shouldTrimString($string, $expected)
    {
        //when
        $result = Strings::trimToNull($string);

        //then
        $this->assertSame($expected, $result);
    }

    public function trimToNull(): array
    {
        return [
            ['  sdf ', 'sdf'],
            ['   ', null],
            [null, null],
        ];
    }

    /**
     * @test
     * @dataProvider sprintAssoc
     */
    public function shouldSprintfStringWithAssocArrayAsParam($sprintfString, $assocArray, $expected)
    {
        //when
        $resultString = Strings::sprintAssoc($sprintfString, $assocArray);

        //then
        $this->assertSame($expected, $resultString);
    }

    public function sprintAssoc(): array
    {
        return [
            ['This is %{what}! %{what}? This is %{place}!', ['what' => 'madness', 'place' => 'Sparta'], 'This is madness! madness? This is Sparta!'],
            ['This is %{what}! %{what}? This is %{place}! And %{invalid_placeholder}!', ['what' => 'madness', 'place' => 'Sparta'], 'This is madness! madness? This is Sparta! And %{invalid_placeholder}!'],
            [null, ['what' => 'madness', 'place' => 'Sparta'], null],
            ['This is %{what}! %{what}? This is %{place}!', null, 'This is %{what}! %{what}? This is %{place}!'],
        ];
    }

    /**
     * @test
     * @dataProvider sprintAssocDefault
     */
    public function shouldSprintfStringAndReplaceWithEmptyIfNoPlaceholderFound($sprintfString, $assocArray, $default, $expected)
    {
        //when
        $resultString = Strings::sprintAssocDefault($sprintfString, $assocArray, $default);

        //then
        $this->assertSame($expected, $resultString);
    }

    public function sprintAssocDefault(): array
    {
        return [
            ['This is %{what}! This is %{place}! No, this is invalid %{invalid_placeholder} placeholder!', ['what' => 'madness', 'place' => 'Sparta'], '', 'This is madness! This is Sparta! No, this is invalid  placeholder!'],
            ['This is %{what}! This is %{place}! No, this is invalid %{invalid_placeholder} placeholder!', ['what' => 'madness', 'place' => 'Sparta'], 'tomato', 'This is madness! This is Sparta! No, this is invalid tomato placeholder!'],
            [null, ['what' => 'madness', 'place' => 'Sparta'], 'tomato', null],
            ['This is %{what}! This is %{place}! No, this is invalid %{invalid_placeholder} placeholder!', null, 'tomato', 'This is tomato! This is tomato! No, this is invalid tomato placeholder!'],
            ['This is %{what}! This is %{place}! No, this is invalid %{invalid_placeholder} placeholder!', null, null, 'This is %{what}! This is %{place}! No, this is invalid %{invalid_placeholder} placeholder!'],
        ];
    }

    /**
     * @test
     * @dataProvider contains
     */
    public function shouldCheckStringContainsSubstring($string, $substring, $expected)
    {
        //when
        $contains = Strings::contains($string, $substring);

        //then
        $this->assertSame($expected, $contains);
    }

    public function contains(): array
    {
        return [
            ['Fear cuts deeper than swords', 'deeper', true],
            ['Fear cuts deeper than swords', 'DeEpEr', false],
            [null, 'DeEpEr', false],
            ['Fear cuts deeper than swords', null, false],
            [null, null, false],
        ];
    }

    /**
     * @test
     * @dataProvider containsIgnoreCase
     */
    public function shouldCheckStringContainsSubstringIgnoringCase($string, $substring, $expected)
    {
        //when
        $contains = Strings::containsIgnoreCase($string, $substring);

        //then
        $this->assertSame($expected, $contains);
    }

    public function containsIgnoreCase(): array
    {
        return [
            ['Fear cuts deeper than swords', 'deeper', true],
            ['Fear cuts deeper than swords', 'DeEpEr', true],
            [null, 'DeEpEr', false],
            ['Fear cuts deeper than swords', null, false],
            [null, null, false],
        ];
    }

    /**
     * @test
     * @dataProvider substringBefore
     */
    public function shouldGetSubstringBeforeSeparator($string, $separator, $expected)
    {
        //when
        $result = Strings::substringBefore($string, $separator);

        //then
        $this->assertSame($expected, $result);
    }

    public function substringBefore(): array
    {
        return [
            ['winter is coming???!!!', '?', 'winter is coming'],
            ['winter is coming', ',', 'winter is coming'],
            [null, ',', null],
            ['winter is coming', null, 'winter is coming'],
            [null, null, null],
        ];
    }

    /**
     * @test
     * @dataProvider substringAfter
     */
    public function shouldGetSubstringAfterSeparator($string, $separator, $expected)
    {
        //when
        $result = Strings::substringAfter($string, $separator);

        //then
        $this->assertSame($expected, $result);
    }

    public function substringAfter(): array
    {
        return [
            ['abc+efg+hij', '+', 'efg+hij'],
            ['abc+', '+', ''],
            ['abc', '-', 'abc'],
            [null, '+', null],
            ['abc+efg+hij', null, 'abc+efg+hij'],
            [null, null, null],
        ];
    }

    /**
     * @test
     * @dataProvider replaceNth
     */
    public function shouldReplaceNthString($subject, $search, $replace, $nth, $expected)
    {
        //when
        $replaceNth = Strings::replaceNth($subject, $search, $replace, $nth);

        //then
        $this->assertSame($expected, $replaceNth);
    }

    public function replaceNth(): array
    {
        return [
            ['name = ? AND description =    ?', '\\=\\s*\\?', 'IS NULL', 1, 'name = ? AND description IS NULL'],
            ['name = ? AND description =    ?', 'not there', 'IS NULL', 1, 'name = ? AND description =    ?'],
            [null, 'not there', 'IS NULL', 1, null],
            ['name = ? AND description =    ?', null, 'IS NULL', 1, 'name = ? AND description =    ?'],
            ['name = ? AND description =    ?', '\\=\\s*\\?', null, 1, 'name = ? AND description =    ?'],
        ];
    }

    /**
     * @test
     * @dataProvider removeAccent
     */
    public function shouldRemoveAccents($string, $expected)
    {
        //when
        $removeAccent = Strings::removeAccent($string);

        //then
        $this->assertSame($expected, $removeAccent);
    }

    public function removeAccent(): array
    {
        return [
            ['String with śżźćółŹĘ ÀÁÂ', 'String with szzcolZE AAA'],
            [null, null],
        ];
    }

    /**
     * @test
     * @dataProvider uppercaseFirst
     */
    public function shouldUpperCaseFirstLetter($string, $expected)
    {
        //when
        $uppercaseFirst = Strings::uppercaseFirst($string);

        //then
        $this->assertSame($expected, $uppercaseFirst);
    }

    public function uppercaseFirst(): array
    {
        return [
            ['łukasz', 'Łukasz'],
            ['łuKaSz', 'ŁuKaSz'],
            [null, null],
        ];
    }

    /**
     * @test
     * @dataProvider defaultIfBlank
     */
    public function shouldDefaultIfBlank($string, $default, $expected)
    {
        //when
        $uppercaseFirst = Strings::defaultIfBlank($string, $default);

        //then
        $this->assertSame($expected, $uppercaseFirst);
    }

    public function defaultIfBlank(): array
    {
        return [
            [null, '<NULL>', '<NULL>'],
            ['', '<NULL>', '<NULL>'],
            [' ', '<NULL>', '<NULL>'],
            ['test', '<NULL>', 'test'],
            ['', null, null],
        ];
    }
}
