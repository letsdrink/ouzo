<?php
use Ouzo\Utilities\Json;

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
        $decoded = Json::decode($json);

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
        $json = "{'Organization': 'PHP Documentation Team'}";
        Json::decode($json);

        //when
        $error = Json::lastError();

        //then
        $this->assertEquals(JSON_ERROR_SYNTAX, $error);
    }

    /**
     * @test
     */
    public function shouldCheckStringIsJson()
    {
        //given
        $json = '{"name":"john","id":123,"ip":"127.0.0.1"}';

        //when
        $isJson = Json::isJson($json);

        //then
        $this->assertTrue($isJson);
    }
}