<?php

use Thulium\Db\Stats;

class StatsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldTraceQueryWithParams()
    {
        // given
        $_SESSION = array();
        Stats::reset();

        // when
        $result = Stats::trace('SELECT * FROM table WHERE id = ?', '10', function () {
            return "result";
        });

        // then
        $this->assertEquals("result", $result);
        $this->assertEquals(1, Stats::getNumberOfQueries());

        $queries = Stats::queries();
        $this->assertEquals(Stats::getTotalTime(), $queries[0]['time']);
        $this->assertEquals('SELECT * FROM table WHERE id = ?', $queries[0]['query']);
        $this->assertEquals('10', $queries[0]['params']);
    }
}