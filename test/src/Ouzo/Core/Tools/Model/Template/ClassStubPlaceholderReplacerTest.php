<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tools\Model\Template\ClassStubPlaceholderReplacer;
use Ouzo\Tools\Model\Template\Dialect\Dialect;
use Ouzo\Tools\Model\Template\TableInfo;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ClassStubPlaceholderReplacerTest extends TestCase
{
    #[Test]
    public function shouldDoNotAddTableNameIfIsDefault()
    {
        //given
        /** @var Dialect $dialect */
        $dialect = Mock::mock(Dialect::class);
        Mock::when($dialect)->tableName()->thenReturn('customers');
        Mock::when($dialect)->columns()->thenReturn([]);
        $classStubReplacer = new ClassStubPlaceholderReplacer('Customer', new TableInfo($dialect));

        //when
        $classContents = $classStubReplacer->contents();

        //then
        Assert::thatString($classContents)
            ->isNotEqualTo('')
            ->doesNotContain('{table_table}')
            ->doesNotContain("'table'");
    }

    #[Test]
    public function shouldDoNotAddPrimaryKeyNameIfIsDefault()
    {
        //given
        /** @var Dialect $dialect */
        $dialect = Mock::mock(Dialect::class);
        Mock::when($dialect)->primaryKey()->thenReturn('id');
        Mock::when($dialect)->columns()->thenReturn([]);
        $classStubReplacer = new ClassStubPlaceholderReplacer('Customer', new TableInfo($dialect));

        //when
        $classContents = $classStubReplacer->contents();

        //then
        Assert::thatString($classContents)
            ->isNotEqualTo('')
            ->doesNotContain('{table_primaryKey}')
            ->doesNotContain("'primaryKey'");
    }

    #[Test]
    public function shouldDoNotAddSequenceNameIfIsDefault()
    {
        //given
        /** @var Dialect $dialect */
        $dialect = Mock::mock(Dialect::class);
        Mock::when($dialect)->primaryKey()->thenReturn('id');
        Mock::when($dialect)->tableName()->thenReturn('customers');
        Mock::when($dialect)->sequence()->thenReturn('customers_id_seq');
        Mock::when($dialect)->columns()->thenReturn([]);
        $classStubReplacer = new ClassStubPlaceholderReplacer('Customer', new TableInfo($dialect));

        //when
        $classContents = $classStubReplacer->contents();

        //then
        Assert::thatString($classContents)
            ->isNotEqualTo('')
            ->doesNotContain('{table_sequence}')
            ->doesNotContain("'sequence'");
    }

    #[Test]
    public function shouldAddEmptyPrimaryKeyEntryWhenNoFoundPrimaryKeyInTable()
    {
        //given
        /** @var Dialect $dialect */
        $dialect = Mock::mock(Dialect::class);
        Mock::when($dialect)->primaryKey()->thenReturn('');
        Mock::when($dialect)->columns()->thenReturn([]);
        $classStubReplacer = new ClassStubPlaceholderReplacer('Customer', new TableInfo($dialect));

        //when
        $classContents = $classStubReplacer->contents();

        //then
        Assert::thatString($classContents)
            ->isNotEqualTo('')
            ->contains('primaryKey');
    }
}
