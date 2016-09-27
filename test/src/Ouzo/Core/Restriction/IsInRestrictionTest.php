<?php
use Ouzo\Restrictions;

class IsInRestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateSql()
    {
        //given
        $restriction = Restrictions::isIn(array(1, 2, 3));

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertEquals('category_id IN(?, ?, ?)', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnNullForEmptyArray()
    {
        //given
        $restriction = Restrictions::isIn(array());

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertNull($sql);
    }
}
