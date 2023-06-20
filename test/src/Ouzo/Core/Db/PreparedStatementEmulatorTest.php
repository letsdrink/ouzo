<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db;
use Ouzo\Db\PreparedStatementEmulator;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class PreparedStatementEmulatorTest extends TestCase
{
    #[Test]
    public function shouldSubstituteParametersInSimpleQuery()
    {
        //given
        $sql = 'select * from users where name = ? and surname = ?';
        $params = ['bob', 'smith'];

        //when
        $result = PreparedStatementEmulator::substitute($sql, $params);

        //then
        Assert::thatString($result)->isEqualTo("select * from users where name = 'bob' and surname = 'smith'");
    }

    #[Test]
    public function shouldSubstituteBooleanParameters()
    {
        //given
        $sql = 'select * from users where verified = ? or admin = ?';
        $params = [true, false];

        //when
        $result = PreparedStatementEmulator::substitute($sql, $params);

        //then
        Assert::thatString($result)->isEqualTo("select * from users where verified = true or admin = false");
    }

    #[Test]
    public function shouldConvertNumbersToStrings()
    {
        //given
        $sql = "select * from users where age = ?";
        $params = ['26'];

        //when
        $result = PreparedStatementEmulator::substitute($sql, $params);

        //then
        Assert::thatString($result)->isEqualTo("select * from users where age = '26'");
    }

    #[Test]
    public function shouldNotSubstituteQuestionMarksInStrings()
    {
        //given
        $sql = "select * from users where id = '?'";
        $params = [];

        //when
        $result = PreparedStatementEmulator::substitute($sql, $params);

        //then
        Assert::thatString($result)->isEqualTo($sql);
    }

    #[Test]
    public function shouldSubstituteParametersWhenSqlContainsStrings()
    {
        //given
        $sql = "select * from users where name = 'bob?' and age = ? and  surname = 'smith?'";
        $params = ['26'];

        //when
        $result = PreparedStatementEmulator::substitute($sql, $params);

        //then
        Assert::thatString($result)
            ->isEqualTo("select * from users where name = 'bob?' and age = '26' and  surname = 'smith?'");
    }

    #[Test]
    public function shouldQuoteParams()
    {
        //given
        $sql = "select * from users where surname = ?";
        $param = "' or '1' = '1";

        //when
        $result = PreparedStatementEmulator::substitute($sql, [$param]);

        //then
        Assert::thatString($result)
            ->isEqualTo("select * from users where surname = " . Db::getInstance()->dbHandle->quote($param));
    }
}
