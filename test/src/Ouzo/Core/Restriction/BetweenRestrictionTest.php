<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Restriction\Between;
use Ouzo\Restrictions;
use PHPUnit\Framework\TestCase;

class BetweenRestrictionTest extends TestCase
{
    #[Test]
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::between(1, 3);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('(key >= ? AND key <= ?)', $sql);
        $this->assertEquals([1, 3], $restriction->getValues());
    }

    #[Test]
    public function shouldHandleExclusiveMode()
    {
        //given
        $restriction = Restrictions::between(1, 3, Between::EXCLUSIVE);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('(key > ? AND key < ?)', $sql);
    }

    #[Test]
    public function shouldHandleLeftExclusiveMode()
    {
        //given
        $restriction = Restrictions::between(1, 3, Between::LEFT_EXCLUSIVE);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('(key > ? AND key <= ?)', $sql);
    }

    #[Test]
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
