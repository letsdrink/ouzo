<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\ArrayAssert;
use Ouzo\Tests\Assert;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\JsonDecodeException;
use Ouzo\Utilities\JsonEncodeException;

class JsonTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function shouldDecodeJsonAsArray()
    {
        //given
        $json = '{"name":"john","id":123,"ip":"127.0.0.1"}';

        //when
        $decoded = Json::safeDecode($json, true);

        //then
        ArrayAssert::that($decoded)->hasSize(3)->contains('john', 123, '127.0.0.1');
    }

    /**
     * @test
     */
    public function shouldEncodeArrayToJson()
    {
        //given
        $array = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');

        //when
        $encoded = Json::safeEncode($array);

        //then
        $this->assertEquals('{"key1":"value1","key2":"value2","key3":"value3"}', $encoded);
    }

    /**
     * @test
     */
    public function decodeShouldReturnNullForEmptyString()
    {
        $this->assertNull(Json::safeDecode(''));
    }

    /**
     * @test
     */
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
     */
    public function shouldNotThrowOnInvalidJson($validJson)
    {
        // when
        Json::decode($validJson);
    }

    /**
     * @test
     * @dataProvider invalidJson
     * @param string $invalidJson
     */
    public function shouldThrowOnInvalidJson($invalidJson)
    {
        // when
        try {
            Json::decode($invalidJson);
            $this->assertTrue(false);
        } // then
        catch (JsonDecodeException $e) {
        }
    }

    /**
     * @test
     */
    public function shouldEncodeThrowOnMalformedUtf8Syntax()
    {
        // when
        try {
            Json::encode("\xB1\x31");
            $this->assertTrue(false);
        } // then
        catch (JsonEncodeException $e) {
        }
    }

    /**
     * @test
     */
    public function shouldEncodeThrowOnInfiniteValue()
    {
        // when
        try {
            Json::encode(log(0));
            $this->assertTrue(false);
        } // then
        catch (JsonEncodeException $e) {
        }
    }

    function invalidJson()
    {
        return array(
            array('()'),
            array('(asd)'),
            array('{3}'),
            array('{"3":,"3"}'),
            array('<html>'),
            array('3=3')
        );
    }

    function validJson()
    {
        return array(
            array(''),
            array('0'),
            array('null'),
            array('1'),
            array('true'),
            array('false'),
            array('"test"'),
            array('[1]'),
            array('{"hej":4}'),
        );
    }
}
