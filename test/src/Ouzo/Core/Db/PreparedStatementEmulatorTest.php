<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Db;
use Ouzo\Db\PreparedStatementEmulator;
use Ouzo\Tests\Assert;

class PreparedStatementEmulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldSubstituteParametersInSimpleQuery()
    {
        //given
        $sql = 'select * from users where name = ? and surname = ?';
        $params = array('bob', 'smith');

        //when
        $result = PreparedStatementEmulator::substitute($sql, $params);

        //then
        Assert::thatString($result)->isEqualTo("select * from users where name = 'bob' and surname = 'smith'");
    }

    /**
     * @test
     */
    public function shouldSubstituteBooleanParameters()
    {
        //given
        $sql = 'select * from users where verified = ? or admin = ?';
        $params = array(true, false);

        //when
        $result = PreparedStatementEmulator::substitute($sql, $params);

        //then
        Assert::thatString($result)->isEqualTo("select * from users where verified = true or admin = false");
    }

    /**
     * @test
     */
    public function shouldConvertNumbersToStrings()
    {
        //given
        $sql = "select * from users where age = ?";
        $params = array('26');

        //when
        $result = PreparedStatementEmulator::substitute($sql, $params);

        //then
        Assert::thatString($result)->isEqualTo("select * from users where age = '26'");
    }

    /**
     * @test
     */
    public function shouldNotSubstituteQuestionMarksInStrings()
    {
        //given
        $sql = "select * from users where id = '?'";
        $params = array();

        //when
        $result = PreparedStatementEmulator::substitute($sql, $params);

        //then
        Assert::thatString($result)->isEqualTo($sql);
    }

    /**
     * @test
     */
    public function shouldSubstituteParametersWhenSqlContainsStrings()
    {
        //given
        $sql = "select * from users where name = 'bob?' and age = ? and  surname = 'smith?'";
        $params = array('26');

        //when
        $result = PreparedStatementEmulator::substitute($sql, $params);

        //then
        Assert::thatString($result)->isEqualTo("select * from users where name = 'bob?' and age = '26' and  surname = 'smith?'");
    }

    /**
     * @test
     */
    public function shouldQuoteParams()
    {
        //given
        $sql = "select * from users where surname = ?";
        $param = "' or '1' = '1";

        //when
        $result = PreparedStatementEmulator::substitute($sql, array($param));

        //then
        Assert::thatString($result)->isEqualTo("select * from users where surname = " . Db::getInstance()->_dbHandle->quote($param));
    }
}
