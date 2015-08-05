<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Restrictions;

class IsNotInRestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateSql()
    {
        //given
        $restriction = Restrictions::isNotIn(array(1, 2, 3));

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertEquals('category_id NOT IN(?, ?, ?)', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnNullForEmptyArray()
    {
        //given
        $restriction = Restrictions::isNotIn(array());

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertNull($sql);
    }
}
