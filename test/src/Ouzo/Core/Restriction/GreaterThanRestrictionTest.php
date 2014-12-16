<?php

use Ouzo\Restrictions;

class GreaterThanRestrictionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::greaterThan(5);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key > ?', $sql);
        $this->assertEquals(5, $restriction->getValues());
    }
}
 