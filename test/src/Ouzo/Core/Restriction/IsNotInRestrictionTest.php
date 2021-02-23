<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Restrictions;

use PHPUnit\Framework\TestCase; 

class IsNotInRestrictionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateSql()
    {
        //given
        $restriction = Restrictions::isNotIn([1, 2, 3]);

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertEquals('category_id NOT IN(?, ?, ?)', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringForEmptyArray()
    {
        //given
        $restriction = Restrictions::isNotIn([]);

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertEmpty($sql);
    }
}
