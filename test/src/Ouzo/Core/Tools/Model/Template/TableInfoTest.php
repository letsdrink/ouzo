<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tools\Model\Template\DatabaseColumn;
use Ouzo\Tools\Model\Template\TableInfo;

class TableInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnFieldsWithoutPrimaryKey()
    {
        //given
        $dialect = Mock::mock('Ouzo\Tools\Model\Template\Dialect\Dialect');
        Mock::when($dialect)->primaryKey()->thenReturn('id');
        Mock::when($dialect)->columns()->thenReturn(array(
            new DatabaseColumn('sale', 'text'),
            new DatabaseColumn('description', 'text'),
            new DatabaseColumn('id', 'int'),
        ));
        $tableInfo = new TableInfo($dialect);

        //when
        $columns = $tableInfo->tableColumns;

        //then
        Assert::thatArray($columns)->onProperty('name')->containsOnly('sale', 'description');
    }
}
