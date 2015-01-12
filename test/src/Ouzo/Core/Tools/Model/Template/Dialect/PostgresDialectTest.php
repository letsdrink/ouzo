<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Core\Tools\Model\Template;

use Ouzo\Tools\Model\Template\DatabaseColumn;
use Ouzo\Tools\Model\Template\Dialect\PostgresDialect;
use PHPUnit_Framework_TestCase;

class PostgresDialectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldExtractSequenceNameFromPrimaryKeyDefault()
    {
        //given
        $dialect = new PostgresDialect('test');
        $columns = array(
            'primary' => new DatabaseColumn('primary', 'int', "nextval('test_id_seq'::regclass)")
        );

        //when
        $sequence = $dialect->getSequenceName($columns, 'primary');

        //then
        $this->assertEquals('test_id_seq', $sequence);
    }
}
