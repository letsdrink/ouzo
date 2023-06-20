<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Restrictions;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class GreaterOrEqualToRestrictionTest extends TestCase
{
    #[Test]
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::greaterOrEqualTo(5);

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key >= ?', $sql);
        $this->assertEquals([5], $restriction->getValues());
    }
}
