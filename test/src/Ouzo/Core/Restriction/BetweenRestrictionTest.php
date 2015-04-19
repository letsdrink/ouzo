<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Restriction\Between;
use Ouzo\Restrictions;

class BetweenRestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::between(1, 3);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('(key >= ? AND key <= ?)', $sql);
        $this->assertEquals(array(1, 3), $restriction->getValues());
    }

    /**
     * @test
     */
    public function shouldHandleExclusiveMode()
    {
        //given
        $restriction = Restrictions::between(1, 3, Between::EXCLUSIVE);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('(key > ? AND key < ?)', $sql);
    }

    /**
     * @test
     */
    public function shouldHandleLeftExclusiveMode()
    {
        //given
        $restriction = Restrictions::between(1, 3, Between::LEFT_EXCLUSIVE);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('(key > ? AND key <= ?)', $sql);
    }

    /**
     * @test
     */
    public function shouldHandleRightExclusiveMode()
    {
        //given
        $restriction = Restrictions::between(1, 3, Between::RIGHT_EXCLUSIVE);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('(key >= ? AND key < ?)', $sql);
    }
}
