<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tools\Model\Template\DatabaseColumn;
use Ouzo\Tools\Model\Template\Dialect\Dialect;
use Ouzo\Tools\Model\Template\TableInfo;

use PHPUnit\Framework\TestCase; 

class TableInfoTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnFieldsWithoutPrimaryKeyWhenIsNotDefault()
    {
        //given
        /** @var Dialect $dialect */
        $dialect = Mock::mock(Dialect::class);
        Mock::when($dialect)->primaryKey()->thenReturn('id_name');
        Mock::when($dialect)->columns()->thenReturn([
            new DatabaseColumn('sale', 'text'),
            new DatabaseColumn('description', 'text'),
            new DatabaseColumn('id_name', 'int'),
        ]);
        $tableInfo = new TableInfo($dialect);

        //when
        $columns = $tableInfo->tableColumns;

        //then
        Assert::thatArray($columns)->onProperty('name')->containsOnly('sale', 'description');
    }

    /**
     * @test
     */
    public function shouldReturnFieldsWithPrimaryKeyWhenIsDefault()
    {
        //given
        /** @var Dialect $dialect */
        $dialect = Mock::mock(Dialect::class);
        Mock::when($dialect)->primaryKey()->thenReturn('id');
        Mock::when($dialect)->columns()->thenReturn([
            new DatabaseColumn('sale', 'text'),
            new DatabaseColumn('description', 'text'),
            new DatabaseColumn('id', 'int'),
        ]);
        $tableInfo = new TableInfo($dialect);

        //when
        $columns = $tableInfo->tableColumns;

        //then
        Assert::thatArray($columns)->onProperty('name')->containsOnly('sale', 'description', 'id');
    }
}
