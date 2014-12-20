<?php
use Ouzo\Restrictions;

class NotEqualToRestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::notEqualTo('value');

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key <> ?', $sql);
        $this->assertEquals('value', $restriction->getValues());
    }
}
