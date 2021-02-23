<?php
use Ouzo\Restrictions;

use PHPUnit\Framework\TestCase;

class IsInRestrictionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateSql()
    {
        //given
        $restriction = Restrictions::isIn([1, 2, 3]);

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertEquals('category_id IN(?, ?, ?)', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringForEmptyArray()
    {
        //given
        $restriction = Restrictions::isIn([]);

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertEmpty($sql);
    }
}
