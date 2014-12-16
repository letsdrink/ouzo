<?php

use Ouzo\Restrictions;

class GreaterOrEqualToRestrictionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::greaterOrEqualTo(5);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key >= ?', $sql);
        $this->assertEquals(5, $restriction->getValues());
    }
}
 