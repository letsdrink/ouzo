<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\Stats;
use Ouzo\FrontController;
use Ouzo\Tests\ArrayAssert;
use Ouzo\Utilities\Arrays;
use PHPUnit\Framework\TestCase;

class StatsTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $_SESSION = [];
        Stats::reset();

        FrontController::$requestId = null;
    }

    /**
     * @test
     */
    public function shouldTraceQueryWithParams()
    {
        // when
        $result = Stats::trace('SELECT * FROM table WHERE id = ?', '10', function () {
            sleep(1);
            return "result";
        });

        // then
        $this->assertEquals("result", $result);
        $this->assertCount(1, Stats::$queries);

        $this->assertEquals(Stats::getTotalTime(), Stats::$queries[0]['time']);
        $this->assertNotEquals('0.0000', Stats::$queries[0]['time']);
        $this->assertEquals('SELECT * FROM table WHERE id = ?', Stats::$queries[0]['query']);
        $this->assertEquals('10', Stats::$queries[0]['params']);
    }

    /**
     * @test
     */
    public function shouldGroupByRequest()
    {
        $this->markTestSkipped('move to controller');
        //given
        $this->_createTraceRequest('/request1');
        $this->_createTraceRequest('/request2');
        $this->_createTraceRequest('/request1');

        //when
        $queries = Stats::$queries;

        //then
        ArrayAssert::that($queries)->hasSize(2);
    }

    /**
     * @test
     */
    public function shouldCountTimeAndNumberOfQueries()
    {
        $this->markTestSkipped('move to controller');
        //when
        $this->_createTraceRequest('/request1');
        $this->_createTraceRequest('/request2');
        $this->_createTraceRequest('/request1');

        //then
        $this->assertEquals(2, Stats::getRequestNumberOfQueries('/request1#'));
    }

    /**
     * @test
     */
    public function shouldTraceInfoAboutHttpRequest()
    {
        $this->markTestSkipped('move to controller tests');
        //given
        $this->_createHttpTraceRequest('/request1', ['param1' => 1, 'param2' => 2]);

        //when
        $queries = Arrays::first(Stats::queries());

        //then
        ArrayAssert::that($queries['request_params'][0])->hasSize(2)->containsKeyAndValue([
            'param1' => 1, 'param2' => 2
        ]);
    }

    private function _createTraceRequest($request)
    {
        $_SERVER['REQUEST_URI'] = $request;
        Stats::trace('SELECT * FROM table WHERE id = ?', '10', function () {
            usleep(80000);
            return "result";
        });
    }

    private function _createHttpTraceRequest($request, $params = [])
    {
        $_SERVER['REQUEST_URI'] = $request;
    }
}
