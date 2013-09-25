<?php
use Ouzo\Session;
use Ouzo\Tests\Assert;

class SessionTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $_SESSION = array();
    }

    /**
     * @test
     */
    public function shouldSetSessionValue()
    {
        //given
        $session = new Session('example');

        //when
        $session->set('key', 'value');

        //then
        Assert::thatArray($_SESSION['example'])->contains('value');
    }

    /**
     * @test
     */
    public function shouldSetMultipleSessionValues()
    {
        //given
        $session = new Session('example');

        //when
        $session
            ->set('key1', 'value1')
            ->set('key2', 'value2')
            ->set('key3', 'value3');

        //then
        Assert::thatArray($_SESSION['example'])->hasSize(3);
    }

    /**
     * @test
     */
    public function shouldGetSessionValue()
    {
        //given
        $session = new Session('example');
        $session->set('key', 'value');

        //when
        $value = $session->get('key');

        //then
        $this->assertEquals('value', $value);
    }

    /**
     * @test
     */
    public function shouldDeleteSessionNamespace()
    {
        //given
        $session = new Session('example');
        $session->set('key', 'value');

        //when
        $session->delete();

        //then
        Assert::thatArray($_SESSION)->isEmpty();
    }

    /**
     * @test
     */
    public function shouldPushValueToSessionNamespace()
    {
        //given
        $session = new Session('example');
        $session->set('key', 'value');

        //when
        $session->push('value_pushed');

        //then
        Assert::thatArray($_SESSION['example'])->hasSize(2)->containsOnly('value', 'value_pushed');
    }

    /**
     * @test
     */
    public function shouldGetAllSessionNamespaceEntries()
    {
        //given
        $session = new Session('example');
        $session
            ->set('key1', 'value1')
            ->set('key2', 'value2');

        //when
        $all = $session->all();

        //then
        Assert::thatArray($all)->hasSize(2)->containsOnly('value1', 'value2');
    }
}