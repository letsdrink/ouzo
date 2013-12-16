<?php
use Ouzo\Utilities\Clock;

class ClockTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldResetFreezeDate()
    {
        //given
        Clock::$freezeDate = new DateTime('2013-12-11T15:52:01');

        //when
        Clock::freeze();

        //then
        $this->assertNotEquals('2013-12-11T15:52:01', Clock::nowAsString());
    }
}