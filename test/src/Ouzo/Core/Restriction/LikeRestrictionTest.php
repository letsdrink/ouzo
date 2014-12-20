<?php
use Ouzo\Restrictions;

class LikeRestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::like('value');

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key LIKE ?', $sql);
        $this->assertEquals('value', $restriction->getValues());
    }
}
