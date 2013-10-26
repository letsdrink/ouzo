<?php


namespace Ouzo\Tools\Model\Template;


use Ouzo\Tools\Model\Template\Dialect\PostgresDialect;

class PostgresDialectTest extends \PHPUnit_Framework_TestCase
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
 