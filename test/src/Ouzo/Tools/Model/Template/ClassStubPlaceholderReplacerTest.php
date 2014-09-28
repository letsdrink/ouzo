<?php
namespace Ouzo\Tools\Model\Template;

use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;
use PHPUnit_Framework_TestCase;

class ClassStubPlaceholderReplacerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldDoNotAddTableNameIfIsDefault()
    {
        //given
        $dialect = Mock::mock('Ouzo\Tools\Model\Template\Dialect\Dialect');
        Mock::when($dialect)->tableName()->thenReturn('customers');
        Mock::when($dialect)->columns()->thenReturn(array());
        $classStubReplacer = new ClassStubPlaceholderReplacer('Customer', new TableInfo($dialect));

        //when
        $classContents = $classStubReplacer->contents();

        //then
        Assert::thatString($classContents)
            ->isNotEqualTo('')
            ->doesNotContain('{table_table}')
            ->doesNotContain("'table'");
    }

    /**
     * @test
     */
    public function shouldDoNotAddPrimaryKeyNameIfIsDefault()
    {
        //given
        $dialect = Mock::mock('Ouzo\Tools\Model\Template\Dialect\Dialect');
        Mock::when($dialect)->primaryKey()->thenReturn('id');
        Mock::when($dialect)->columns()->thenReturn(array());
        $classStubReplacer = new ClassStubPlaceholderReplacer('Customer', new TableInfo($dialect));

        //when
        $classContents = $classStubReplacer->contents();

        //then
        Assert::thatString($classContents)
            ->isNotEqualTo('')
            ->doesNotContain('{table_primaryKey}')
            ->doesNotContain("'primaryKey'");
    }

    /**
     * @test
     */
    public function shouldDoNotAddSequenceNameIfIsDefault()
    {
        //given
        $dialect = Mock::mock('Ouzo\Tools\Model\Template\Dialect\Dialect');
        Mock::when($dialect)->primaryKey()->thenReturn('id');
        Mock::when($dialect)->tableName()->thenReturn('customers');
        Mock::when($dialect)->sequence()->thenReturn('customers_id_seq');
        Mock::when($dialect)->columns()->thenReturn(array());
        $classStubReplacer = new ClassStubPlaceholderReplacer('Customer', new TableInfo($dialect));

        //when
        $classContents = $classStubReplacer->contents();

        //then
        Assert::thatString($classContents)
            ->isNotEqualTo('')
            ->doesNotContain('{table_sequence}')
            ->doesNotContain("'sequence'");
    }

    /**
     * @test
     */
    public function shouldAddEmptyPrimaryKeyEntryWhenNoFoundPrimaryKeyInTable()
    {
        //given
        $dialect = Mock::mock('Ouzo\Tools\Model\Template\Dialect\Dialect');
        Mock::when($dialect)->primaryKey()->thenReturn('');
        Mock::when($dialect)->columns()->thenReturn(array());
        $classStubReplacer = new ClassStubPlaceholderReplacer('Customer', new TableInfo($dialect));

        //when
        $classContents = $classStubReplacer->contents();

        //then
        Assert::thatString($classContents)
            ->isNotEqualTo('')
            ->contains('primaryKey');
    }
}