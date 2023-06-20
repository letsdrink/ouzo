<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\Assert;
use Ouzo\Tools\Model\Template\ClassStub;
use Ouzo\Tools\Model\Template\DatabaseColumn;
use PHPUnit\Framework\TestCase;

class ClassStubTest extends TestCase
{
    #[Test]
    public function shouldReplacePlaceholderInStubFile()
    {
        //given
        $classStub = new ClassStub();

        //when
        $classStub->replacePlaceholders(['class' => 'TestClassName', 'fields' => 'fieldA, fieldB']);

        //then
        $this->assertStringContainsString('TestClassName', $classStub->contents());
        $this->assertStringContainsString('fieldA, fieldB', $classStub->contents());
    }

    #[Test]
    public function shouldAddPropertyWithType()
    {
        //given
        $classStub = new ClassStub();

        //when
        $classStub->addColumn(new DatabaseColumn('test_field', 'string'));

        //then
        $this->assertStringContainsString('test_field', $classStub->contents());
    }

    #[Test]
    public function shouldReplaceAllPlaceholders()
    {
        //given
        $classStub = new ClassStub();

        //when
        $classStub
            ->addPlaceholderReplacement('fields', 'fields')
            ->addPlaceholderReplacement('properties', 'properties')
            ->addPlaceholderReplacement('class', 'class')
            ->addPlaceholderReplacement('namespace', '/User')
            ->addPlaceholderReplacement('table_table', 'table_table')
            ->addPlaceholderReplacement('table_primaryKey', 'table_primaryKey')
            ->addPlaceholderReplacement('table_sequence', 'table_sequence');

        //then
        $this->assertDoesNotMatchRegularExpression('/{(\w*)}/', $classStub->contents());
    }

    #[Test]
    public function shouldSplitFieldsToNewLines()
    {
        //given
        $classStub = new ClassStub();

        //when
        $classStub
            ->addColumn(new DatabaseColumn('field1', 'string'))
            ->addColumn(new DatabaseColumn('field2', 'string'))
            ->addColumn(new DatabaseColumn('field3', 'string'))
            ->addColumn(new DatabaseColumn('field4', 'string'))
            ->addColumn(new DatabaseColumn('field5', 'string'))
            ->addColumn(new DatabaseColumn('field6', 'string'))
            ->addColumn(new DatabaseColumn('field7', 'string'))
            ->addColumn(new DatabaseColumn('field8', 'string'))
            ->addColumn(new DatabaseColumn('field9', 'string'));

        //then
        $this->assertStringContainsString("'field7', \n", $classStub->getFieldsAsString());
    }

    #[Test]
    public function shouldGenerateClassWithShortArrays()
    {
        //given
        $classStub = new ClassStub(true);

        //when
        $classStub
            ->addColumn(new DatabaseColumn('field1', 'string'))
            ->addColumn(new DatabaseColumn('field2', 'string'));

        //then
        Assert::thatString($classStub->contents())
            ->contains("['field1', 'field2']")
            ->contains('$attributes = []')
            ->contains('parent::__construct([
            {table_table}');
    }
}
