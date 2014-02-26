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
        //when
        Session::set('key', 'value');

        //then
        Assert::thatSession()
            ->hasSize(1)
            ->containsKeyAndValue(array('key' => 'value'));
    }

    /**
     * @test
     */
    public function shouldSetMultipleSessionValues()
    {
        //when
        Session::set('key1', 'value1')
            ->set('key2', 'value2')
            ->set('key3', 'value3');

        //then
        Assert::thatSession()
            ->hasSize(3)
            ->containsKeyAndValue(array(
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3'));
    }

    /**
     * @test
     */
    public function shouldGetSessionValue()
    {
        //given
        Session::set('key', 'value');

        //when
        $value = Session::get('key');

        //then
        $this->assertEquals('value', $value);
    }

    /**
     * @test
     */
    public function hasShouldReturnTrueIfItemExistsInSession()
    {
        //given
        Session::set('key', 'value');

        //when
        $value = Session::has('key');

        //then
        $this->assertTrue($value);
    }

    /**
     * @test
     */
    public function hasShouldReturnFalseIfItemDoesNotExistInSession()
    {
        //when
        $value = Session::has('key');

        //then
        $this->assertFalse($value);
    }

    /**
     * @test
     */
    public function shouldFlushSession()
    {
        //given
        Session::set('key', 'value');

        //when
        Session::flush();

        //then
        Assert::thatSession()->isEmpty();
    }

    /**
     * @test
     */
    public function shouldGetAllValuesFromSession()
    {
        //given
        Session::set('key', 'value');

        //when
        $all = Session::all();

        //then
        Assert::thatArray($all)
            ->hasSize(1)
            ->containsKeyAndValue(array('key' => 'value'));
    }

    /**
     * @test
     */
    public function shouldNotClashNamespaces()
    {
        //given
        Session::forNamespace('first')->set('key1', 'value1');
        Session::forNamespace('second')->set('key2', 'value2');

        //when
        $value1 = Session::forNamespace('first')->get('key1');
        $value2 = Session::forNamespace('second')->get('key2');

        //then
        $this->assertEquals('value1', $value1);
        $this->assertEquals('value2', $value2);
    }
}