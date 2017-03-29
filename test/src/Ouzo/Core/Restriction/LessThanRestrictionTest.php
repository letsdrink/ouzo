<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Restrictions;

class LessThanRestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::lessThan(5);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key < ?', $sql);
        $this->assertEquals([5], $restriction->getValues());
    }
}
