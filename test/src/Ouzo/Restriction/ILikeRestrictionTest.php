<?php

use Ouzo\Restrictions;

class ILikeRestrictionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::iLike('value');

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key ILIKE ?', $sql);
        $this->assertEquals('value', $restriction->getValues());
    }
}
 