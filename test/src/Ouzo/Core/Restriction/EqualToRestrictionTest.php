<?php
use Ouzo\Restrictions;

class EqualToRestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::equalTo('value');

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key = ?', $sql);
        $this->assertEquals('value', $restriction->getValues());
    }
}
