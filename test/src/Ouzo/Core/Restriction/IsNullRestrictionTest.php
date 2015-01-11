<?php
use Ouzo\Restrictions;
use Ouzo\Tests\CatchException;

class IsNullRestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateSql()
    {
        //given
        $restriction = Restrictions::isNull();

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertEquals('category_id IS NULL', $sql);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenTryGetValues()
    {
        //given
        $restriction = Restrictions::isNull();

        //when
        CatchException::when($restriction)->getValues();

        //then
        CatchException::assertThat()->hasMessage('This type of restriction has no value');
    }
}
