<?php

namespace Ouzo\Tools\Model\Template;

use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;

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
 