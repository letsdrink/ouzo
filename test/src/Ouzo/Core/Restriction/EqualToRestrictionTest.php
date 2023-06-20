<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Restrictions;
use PHPUnit\Framework\TestCase;

class EqualToRestrictionTest extends TestCase
{
    #[Test]
    public function shouldCreateProperSql()
    {
        //given
        $restriction = Restrictions::equalTo('value');

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key = ?', $sql);
        $this->assertEquals(['value'], $restriction->getValues());
    }
}
