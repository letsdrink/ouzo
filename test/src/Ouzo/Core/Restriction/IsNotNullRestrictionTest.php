<?php
use Ouzo\Restrictions;
use Ouzo\Tests\CatchException;

class IsNotNullRestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateSql()
    {
        //given
        $restriction = Restrictions::isNotNull();

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertEquals('category_id IS NOT NULL', $sql);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenTryGetValues()
    {
        //given
        $restriction = Restrictions::isNotNull();

        //when
        CatchException::when($restriction)->getValues();

        //then
        CatchException::assertThat()->hasMessage('This type of restriction has not value');
    }
}