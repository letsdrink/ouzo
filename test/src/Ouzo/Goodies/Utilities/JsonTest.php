<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\ArrayAssert;
use Ouzo\Tests\CatchException;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\JsonDecodeException;
use Ouzo\Utilities\JsonEncodeException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    #[Test]
    public function shouldDecodeJsonToObject()
    {
        //given
        $json = '{"name":"john","id":123,"ip":"127.0.0.1"}';

        //when
        $decoded = Json::safeDecode($json);

        //then
        $this->assertEquals('john', $decoded->name);
        $this->assertEquals('123', $decoded->id);
        $this->assertEquals('127.0.0.1', $decoded->ip);
    }

    #[Test]
    public function shouldDetectJsonError()
    {
        //given
        $json = "{'Organization':error 'PHP Documentation Team'}";
        Json::safeDecode($json);

        //when
        $error = Json::lastError();

        //then
        $this->assertEquals(JSON_ERROR_SYNTAX, $error);
    }

    #[Test]
    public function shouldDecodeJsonAsArray()
    {
        //given
        $json = '{"name":"john","id":123,"ip":"127.0.0.1"}';

        //when
        $decoded = Json::safeDecode($json, true);

        //then
        ArrayAssert::that($decoded)->hasSize(3)->contains('john', 123, '127.0.0.1');
    }

    #[Test]
    public function shouldEncodeArrayToJson()
    {
        //given
        $array = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];

        //when
        $encoded = Json::safeEncode($array);

        //then
        $this->assertEquals('{"key1":"value1","key2":"value2","key3":"value3"}', $encoded);
    }

    #[Test]
    public function decodeShouldReturnNullForEmptyString()
    {
        $this->assertNull(Json::safeDecode(''));
    }

    #[Test]
    public function shouldResetJsonError()
    {
        //given
        Json::safeDecode("error");
        Json::safeDecode("");

        //when
        $error = Json::lastError();

        //then
        $this->assertEquals(JSON_ERROR_NONE, $error);
    }

    /**
     * @test
     * @dataProvider validJson
     * @param string $validJson
     * @throws Throwable
     */
    public function shouldNotThrowOnInvalidJson($validJson)
    {
        //when
        CatchException::when(new Json())->decode($validJson);

        //then
        CatchException::assertThat()->notCaught();
    }

    /**
     * @test
     * @dataProvider invalidJson
     * @param string $invalidJson
     * @throws Exception
     */
    public function shouldThrowOnInvalidJson($invalidJson)
    {
        //when
        CatchException::when(new Json())->decode($invalidJson);

        //then
        CatchException::assertThat()->isInstanceOf(JsonDecodeException::class);
    }

    #[Test]
    public function shouldEncodeThrowOnMalformedUtf8Syntax()
    {
        // when
        try {
            Json::encode("\xB1\x31");
            $this->assertTrue(false);
        } // then
        catch (Exception $e) {
            $this->assertTrue(in_array(get_class($e), [
                PHPUnit_Framework_Error_Warning::class,
                JsonEncodeException::class
            ]));
        }
    }

    #[Test]
    public function shouldEncodeThrowOnInfiniteValue()
    {
        // when
        try {
            Json::encode(log(0));
            $this->assertTrue(false);
        } // then
        catch (Exception $e) {
            $this->assertTrue(in_array(get_class($e), [
                PHPUnit_Framework_Error_Warning::class,
                JsonEncodeException::class
            ]));
        }
    }

    function invalidJson(): array
    {
        return [
            ['()'],
            ['(asd)'],
            ['{3}'],
            ['{"3":,"3"}'],
            ['<html>'],
            ['3=3']
        ];
    }

    function validJson(): array
    {
        return [
            [''],
            ['0'],
            ['null'],
            ['1'],
            ['true'],
            ['false'],
            ['"test"'],
            ['[1]'],
            ['{"hej":4}'],
        ];
    }
}
