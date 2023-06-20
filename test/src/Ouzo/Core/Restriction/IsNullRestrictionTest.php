<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Restrictions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IsNullRestrictionTest extends TestCase
{
    #[Test]
    public function shouldCreateSql()
    {
        //given
        $restriction = Restrictions::isNull();

        //when
        $sql = $restriction->toSql('category_id');

        //then
        $this->assertEquals('category_id IS NULL', $sql);
    }
}
