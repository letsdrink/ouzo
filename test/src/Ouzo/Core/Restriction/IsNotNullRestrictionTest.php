<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Restrictions;

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
}
