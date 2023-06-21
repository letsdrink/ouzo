<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Strings;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StringsTest extends TestCase
{
    #[Test]
    #[DataProvider('underscoreToCamelCase')]
    public function shouldConvertUnderscoreToCamelCase($string, $expected): void
    {
        //when
        $camelcase = Strings::underscoreToCamelCase($string);

        //then
        $this->assertSame($expected, $camelcase);
    }

    public static function underscoreToCamelCase(): array
    {
        return [
            ['lannisters_always_pay_their_debts', 'LannistersAlwaysPayTheirDebts'],
            ['lannistersAlways_pay_their_debts', 'LannistersAlwaysPayTheirDebts'],
            [null, null],
        ];
    }

    #[Test]
    #[DataProvider('camelCaseToUnderscore')]
    public function shouldConvertCamelCaseToUnderscore($string, $expected): void
    {
        //when
        $underscored = Strings::camelCaseToUnderscore($string);

        //then
        $this->assertSame($expected, $underscored);
    }

    public static function camelCaseToUnderscore(): array
    {
        return [
            ['LannistersĄlwaysPayTheirDebtsĘlephant', 'lannisters_ąlways_pay_their_debts_ęlephant'],
            ['LannistersAlwaysPay_Their_Debts', 'lannisters_always_pay_their_debts'],
            ['SomeComplicatedRfc222Name', 'some_complicated_rfc222_name'],
            ['WhatIsOAuth', 'what_is_o_auth'],
            [null, null],
        ];
    }

    #[Test]
    #[DataProvider('removePrefix')]
    public function shouldRemovePrefix($string, $prefix, $expected): void
    {
        //when
        $withoutPrefix = Strings::removePrefix($string, $prefix);

        //then
        $this->assertSame($expected, $withoutPrefix);
    }

    public static function removePrefix(): array
    {
        return [
            ['prefixRest', 'prefix', 'Rest'],
            ['prefix', 'prefix', ''],
            ['prefixRest', null, 'prefixRest'],
            [null, 'prefix', null],
        ];
    }

    #[Test]
    #[DataProvider('removePrefixes')]
    public function shouldRemovePrefixes($string, $prefixes, $expected): void
    {
        //when
        $withoutPrefix = Strings::removePrefixes($string, $prefixes);

        //then
        $this->assertSame($expected, $withoutPrefix);
    }

    public static function removePrefixes(): array
    {
        return [
            ['prefixRest', ['pre', 'fix'], 'Rest'],
            [null, ['pre', 'fix'], null],
            ['prefixRest', [], 'prefixRest'],
            ['prefixRest', null, 'prefixRest'],
        ];
    }

    #[Test]
    #[DataProvider('removeSuffix')]
    public function shouldRemoveSuffix($string, $suffix, $expected): void
    {
        //when
        $withoutSuffix = Strings::removeSuffix($string, $suffix);

        //then
        $this->assertSame($expected, $withoutSuffix);
    }

    public static function removeSuffix(): array
    {
        return [
            ['JohnSnow', 'Snow', 'John'],
            ['JohnSnow', null, 'JohnSnow'],
            [null, 'Snow', null],
        ];
    }

    #[Test]
    #[DataProvider('startsWith')]
    public function shouldReturnTrueIfStringStartsWithPrefix($string, $prefix, $expected): void
    {
        //when
        $result = Strings::startsWith($string, $prefix);

        //then
        $this->assertSame($expected, $result);
    }

    public static function startsWith(): array
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

    #[Test]
    #[DataProvider('endsWith')]
    public function shouldReturnTrueIfStringEndsWithPrefix($string, $suffix, $expected): void
    {
        //when
        $result = Strings::endsWith($string, $suffix);

        //then
        $this->assertSame($expected, $result);
    }

    public static function endsWith(): array
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

    #[Test]
    #[DataProvider('equalsIgnoreCase')]
    public function shouldCheckEqualityIgnoringCase($string1, $string2, $expected): void
    {
        //when
        $result = Strings::equalsIgnoreCase($string1, $string2);

        //then
        $this->assertSame($expected, $result, "Equality failed for '{$string1}' and '{$string2}'");
    }

    public static function equalsIgnoreCase(): array
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

    #[Test]
    #[DataProvider('remove')]
    public function shouldRemovePartOfString($string, $toRemove, $expected): void
    {
        //when
        $result = Strings::remove($string, $toRemove);

        //then
        $this->assertSame($expected, $result);
    }

    public static function remove(): array
    {
        return [
            ['winter is coming???!!!', '???', 'winter is coming!!!'],
            [null, '???', null],
            ['winter is coming???!!!', null, 'winter is coming???!!!'],
        ];
    }

    #[Test]
    #[DataProvider('appendSuffix')]
    public function shouldAppendSuffix($string, $suffix, $expected): void
    {
        //when
        $stringWithSuffix = Strings::appendSuffix($string, $suffix);

        //then
        $this->assertSame($expected, $stringWithSuffix);
    }

    public static function appendSuffix(): array
    {
        return [
            ['Daenerys', ' Targaryen', 'Daenerys Targaryen'],
            [null, ' Targaryen', null],
            ['Daenerys', null, 'Daenerys'],
        ];
    }

    #[Test]
    #[DataProvider('appendPrefix')]
    public function shouldAppendPrefix($string, $prefix, $expected): void
    {
        //when
        $stringWithSuffix = Strings::appendPrefix($string, $prefix);

        //then
        $this->assertSame($expected, $stringWithSuffix);
    }

    public static function appendPrefix(): array
    {
        return [
            ['Targaryen', 'Daenerys ', 'Daenerys Targaryen'],
            [null, ' Daenerys ', null],
            ['Targaryen', null, 'Targaryen'],
        ];
    }

    #[Test]
    #[DataProvider('appendIfMissing')]
    public function shouldAppendSuffixIfNecessary($string, $suffix, $expected): void
    {
        // when
        $result = Strings::appendIfMissing($string, $suffix);

        // then
        $this->assertSame($expected, $result);
    }

    public static function appendIfMissing(): array
    {
        return [
            ['You know nothing, Jon Snow', ', Jon Snow', 'You know nothing, Jon Snow'],
            ['You know nothing', ', Jon Snow', 'You know nothing, Jon Snow'],
            [null, ', Jon Snow', null],
            ['You know nothing', null, 'You know nothing'],
        ];
    }

    #[Test]
    #[DataProvider('prependIfMissing')]
    public function shouldAppendPrefixIfNecessary($string, $prefix, $expected): void
    {
        // when
        $original = Strings::prependIfMissing($string, $prefix);

        // then
        $this->assertSame($expected, $original);
    }

    public static function prependIfMissing(): array
    {
        return [
            ['Khal Drogo', 'Khal ', 'Khal Drogo'],
            ['Drogo', 'Khal ', 'Khal Drogo'],
            [null, 'Khal ', null],
            ['Drogo', null, 'Drogo'],
        ];
    }

    #[Test]
    #[DataProvider('tableize')]
    public function shouldTableizeSimpleClassName($class, $expected): void
    {
        //when
        $table = Strings::tableize($class);

        //then
        $this->assertSame($expected, $table);
    }

    public static function tableize(): array
    {
        return [
            ['Dragon', 'dragons'],
            ['BigFoot', 'big_feet'],
            ['', ''],
            [null, null],
        ];
    }

    #[Test]
    #[DataProvider('escapeNewLines')]
    public function shouldEscapeNewLines($string, $expected): void
    {
        //when
        $escaped = Strings::escapeNewLines($string);

        //then
        $this->assertSame($expected, $escaped);
    }

    public static function escapeNewLines(): array
    {
        return [
            ["My name is <strong>Reek</strong> \nit rhymes with leek", "My name is &lt;strong&gt;Reek&lt;/strong&gt; <br />\nit rhymes with leek"],
            ['', ''],
            [null, null],
        ];
    }

    #[Test]
    #[DataProvider('htmlEntities')]
    public function shouldConvertEntitiesWithUtfChars($string, $expected): void
    {
        //when
        $entities = Strings::htmlEntities($string);

        //then
        $this->assertSame($expected, $entities);
    }

    public static function htmlEntities(): array
    {
        return [
            ['<strong>someting</strong> with ó', '&lt;strong&gt;someting&lt;/strong&gt; with ó'],
            ['', ''],
            [null, null],
        ];
    }

    #[Test]
    #[DataProvider('equal')]
    public function shouldReturnTrueForObjectWithTheSameStringRepresentation($object1, $object2, $expected): void
    {
        //when
        $result = Strings::equal($object1, $object2);

        //then
        $this->assertSame($expected, $result);
    }

    public static function equal(): array
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

    #[Test]
    #[DataProvider('isBlank')]
    public function shouldReturnFalseForNotBlankString($string, $expected): void
    {
        //when
        $result = Strings::isBlank($string);

        //then
        $this->assertSame($expected, $result);
    }

    public static function isBlank(): array
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

    #[Test]
    #[DataProvider('isNotBlank')]
    public function shouldTestIfStringIsNotBlank($string, $expected): void
    {
        //when
        $result = Strings::isNotBlank($string);

        //then
        $this->assertSame($expected, $result);
    }

    public static function isNotBlank(): array
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

    #[Test]
    #[DataProvider('abbreviate')]
    public function shouldAbbreviateString($string, $maxWidth, $expected): void
    {
        //when
        $abbreviated = Strings::abbreviate($string, $maxWidth);

        //then
        $this->assertSame($expected, $abbreviated);
    }

    public static function abbreviate(): array
    {
        return [
            ['ouzo is great', 5, 'ouzo ...'],
            ['ouzo is great', 13, 'ouzo is great'],
            [null, 5, null],
        ];
    }

    #[Test]
    #[DataProvider('trimToNull')]
    public function shouldTrimString($string, $expected): void
    {
        //when
        $result = Strings::trimToNull($string);

        //then
        $this->assertSame($expected, $result);
    }

    public static function trimToNull(): array
    {
        return [
            ['  sdf ', 'sdf'],
            ['   ', null],
            [null, null],
        ];
    }

    #[Test]
    #[DataProvider('sprintAssoc')]
    public function shouldSprintfStringWithAssocArrayAsParam($sprintfString, $assocArray, $expected): void
    {
        //when
        $resultString = Strings::sprintAssoc($sprintfString, $assocArray);

        //then
        $this->assertSame($expected, $resultString);
    }

    public static function sprintAssoc(): array
    {
        return [
            ['This is %{what}! %{what}? This is %{place}!', ['what' => 'madness', 'place' => 'Sparta'], 'This is madness! madness? This is Sparta!'],
            ['This is %{what}! %{what}? This is %{place}! And %{invalid_placeholder}!', ['what' => 'madness', 'place' => 'Sparta'], 'This is madness! madness? This is Sparta! And %{invalid_placeholder}!'],
            [null, ['what' => 'madness', 'place' => 'Sparta'], null],
            ['This is %{what}! %{what}? This is %{place}!', null, 'This is %{what}! %{what}? This is %{place}!'],
            ['This is %{what}! %{what}? This is %{place}!', ['what' => 'madness', 'place' => null, null => 'Sparta'], 'This is madness! madness? This is %{place}!'],
        ];
    }

    #[Test]
    #[DataProvider('sprintAssocDefault')]
    public function shouldSprintfStringAndReplaceWithEmptyIfNoPlaceholderFound($sprintfString, $assocArray, $default, $expected): void
    {
        //when
        $resultString = Strings::sprintAssocDefault($sprintfString, $assocArray, $default);

        //then
        $this->assertSame($expected, $resultString);
    }

    public static function sprintAssocDefault(): array
    {
        return [
            ['This is %{what}! This is %{place}! No, this is invalid %{invalid_placeholder} placeholder!', ['what' => 'madness', 'place' => 'Sparta'], '', 'This is madness! This is Sparta! No, this is invalid  placeholder!'],
            ['This is %{what}! This is %{place}! No, this is invalid %{invalid_placeholder} placeholder!', ['what' => 'madness', 'place' => 'Sparta'], 'tomato', 'This is madness! This is Sparta! No, this is invalid tomato placeholder!'],
            [null, ['what' => 'madness', 'place' => 'Sparta'], 'tomato', null],
            ['This is %{what}! This is %{place}! No, this is invalid %{invalid_placeholder} placeholder!', null, 'tomato', 'This is tomato! This is tomato! No, this is invalid tomato placeholder!'],
            ['This is %{what}! This is %{place}! No, this is invalid %{invalid_placeholder} placeholder!', null, null, 'This is %{what}! This is %{place}! No, this is invalid %{invalid_placeholder} placeholder!'],
        ];
    }

    #[Test]
    #[DataProvider('contains')]
    public function shouldCheckStringContainsSubstring($string, $substring, $expected): void
    {
        //when
        $contains = Strings::contains($string, $substring);

        //then
        $this->assertSame($expected, $contains);
    }

    public static function contains(): array
    {
        return [
            ['Fear cuts deeper than swords', 'deeper', true],
            ['Fear cuts deeper than swords', 'DeEpEr', false],
            [null, 'DeEpEr', false],
            ['Fear cuts deeper than swords', null, false],
            [null, null, false],
        ];
    }

    #[Test]
    #[DataProvider('containsIgnoreCase')]
    public function shouldCheckStringContainsSubstringIgnoringCase($string, $substring, $expected): void
    {
        //when
        $contains = Strings::containsIgnoreCase($string, $substring);

        //then
        $this->assertSame($expected, $contains);
    }

    public static function containsIgnoreCase(): array
    {
        return [
            ['Fear cuts deeper than swords', 'deeper', true],
            ['Fear cuts deeper than swords', 'DeEpEr', true],
            [null, 'DeEpEr', false],
            ['Fear cuts deeper than swords', null, false],
            [null, null, false],
        ];
    }

    #[Test]
    #[DataProvider('substringBefore')]
    public function shouldGetSubstringBeforeSeparator($string, $separator, $expected): void
    {
        //when
        $result = Strings::substringBefore($string, $separator);

        //then
        $this->assertSame($expected, $result);
    }

    public static function substringBefore(): array
    {
        return [
            ['winter is coming???!!!', '?', 'winter is coming'],
            ['winter is coming', ',', 'winter is coming'],
            [null, ',', null],
            ['winter is coming', null, 'winter is coming'],
            [null, null, null],
        ];
    }

    #[Test]
    #[DataProvider('substringAfter')]
    public function shouldGetSubstringAfterSeparator($string, $separator, $expected): void
    {
        //when
        $result = Strings::substringAfter($string, $separator);

        //then
        $this->assertSame($expected, $result);
    }

    public static function substringAfter(): array
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

    #[Test]
    #[DataProvider('replaceNth')]
    public function shouldReplaceNthString($subject, $search, $replace, $nth, $expected): void
    {
        //when
        $replaceNth = Strings::replaceNth($subject, $search, $replace, $nth);

        //then
        $this->assertSame($expected, $replaceNth);
    }

    public static function replaceNth(): array
    {
        return [
            ['name = ? AND description =    ?', '\\=\\s*\\?', 'IS NULL', 1, 'name = ? AND description IS NULL'],
            ['name = ? AND description =    ?', 'not there', 'IS NULL', 1, 'name = ? AND description =    ?'],
            [null, 'not there', 'IS NULL', 1, null],
            ['name = ? AND description =    ?', null, 'IS NULL', 1, 'name = ? AND description =    ?'],
            ['name = ? AND description =    ?', '\\=\\s*\\?', null, 1, 'name = ? AND description =    ?'],
        ];
    }

    #[Test]
    #[DataProvider('removeAccent')]
    public function shouldRemoveAccents($string, $expected): void
    {
        //when
        $removeAccent = Strings::removeAccent($string);

        //then
        $this->assertSame($expected, $removeAccent);
    }

    public static function removeAccent(): array
    {
        return [
            ['String with śżźćółŹĘ ÀÁÂ', 'String with szzcolZE AAA'],
            [null, null],
        ];
    }

    #[Test]
    #[DataProvider('uppercaseFirst')]
    public function shouldUpperCaseFirstLetter($string, $expected): void
    {
        //when
        $uppercaseFirst = Strings::uppercaseFirst($string);

        //then
        $this->assertSame($expected, $uppercaseFirst);
    }

    public static function uppercaseFirst(): array
    {
        return [
            ['łukasz', 'Łukasz'],
            ['łuKaSz', 'ŁuKaSz'],
            [null, null],
        ];
    }

    #[Test]
    #[DataProvider('defaultIfBlank')]
    public function shouldDefaultIfBlank($string, $default, $expected): void
    {
        //when
        $defaultIfBlank = Strings::defaultIfBlank($string, $default);

        //then
        $this->assertSame($expected, $defaultIfBlank);
    }

    public static function defaultIfBlank(): array
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
