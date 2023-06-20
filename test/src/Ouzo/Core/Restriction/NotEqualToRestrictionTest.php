<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Restrictions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class NotEqualToRestrictionTest extends TestCase
{
    #[Test]
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::notEqualTo('value');

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key <> ?', $sql);
        $this->assertEquals(['value'], $restriction->getValues());
    }

    #[Test]
    public function shouldCreateProperSqlForEmptyString()
    {
        //given
        $restriction = Restrictions::notEqualTo('');

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key <> ?', $sql);
        $this->assertEquals([''], $restriction->getValues());
    }
}
