<?php

use Ouzo\Restrictions;

class LessOrEqualToRestrictionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::lessOrEqualTo(5);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key <= ?', $sql);
        $this->assertEquals(5, $restriction->getValues());
    }
}
 