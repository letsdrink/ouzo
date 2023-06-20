<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Restrictions;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class IsInRestrictionTest extends TestCase
{
    #[Test]
    public function shouldCreateSql()
    {
        //given
        $restriction = Restrictions::isIn([1, 2, 3]);

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertEquals('category_id IN(?, ?, ?)', $sql);
    }

    #[Test]
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
