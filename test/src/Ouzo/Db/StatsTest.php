<?php
use Ouzo\Db\Stats;
use Ouzo\Tests\ArrayAssert;

class StatsTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $_SESSION = array();
        Stats::reset();
    }

    /**
     * @test
     */
    public function shouldTraceQueryWithParams()
    {
        // when
        $result = Stats::trace('SELECT * FROM table WHERE id = ?', '10', function () {
            return "result";
        });

        // then
        $this->assertEquals("result", $result);
        $this->assertEquals(1, Stats::getNumberOfQueries());

        $queries = Stats::queries();
        $this->assertEquals(Stats::getTotalTime(), $queries['/'][0]['time']);
        $this->assertEquals('SELECT * FROM table WHERE id = ?', $queries['/'][0]['query']);
        $this->assertEquals('10', $queries['/'][0]['params']);
    }

    /**
     * @test
     */
    public function shouldGroupByRequest()
    {
        //given
        $this->_createTraceRequest('/request1');
        $this->_createTraceRequest('/request2');
        $this->_createTraceRequest('/request1');

        //when
        $queries = Stats::queries();

        //then
        ArrayAssert::that($queries)->hasSize(2);
    }

    private function _createTraceRequest($request)
    {
        $_SERVER['REQUEST_URI'] = $request;
        Stats::trace('SELECT * FROM table WHERE id = ?', '10', function () {
            return "result";
        });
    }
}