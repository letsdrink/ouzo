<?php


namespace Ouzo\Tools\Model\Template;


use Ouzo\Tools\Model\Template\Dialect\MySqlDialect;


class MySqlDialectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldMapIntQslDataTypeToPhpDataType()
    {
        //given
        $dialect = new MySqlDialect('test');

        //when
        $phpIntType1 = $dialect->dataTypeToPhpType('tinyint');
        $phpIntType2 = $dialect->dataTypeToPhpType('int');

        //then
        $this->assertEquals('int', $phpIntType1);
        $this->assertEquals('int', $phpIntType2);
    }

    /**
     * @test
     */
    public function shouldMapFloatQslDataTypeToPhpDataType()
    {
        //given
        $dialect = new MySqlDialect('test');

        //when
        $phpFloatType1 = $dialect->dataTypeToPhpType('FLOAT');
        $phpFloatType2 = $dialect->dataTypeToPhpType('DECIMAL');
        $phpFloatType3 = $dialect->dataTypeToPhpType('DOUBLE');

        //then
        $this->assertEquals('float', $phpFloatType1);
        $this->assertEquals('float', $phpFloatType2);
        $this->assertEquals('float', $phpFloatType3);

    }
}
 