<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Assert;
use Ouzo\Tools\Model\Template\ClassStub;
use Ouzo\Tools\Model\Template\DatabaseColumn;

class ClassStubTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReplacePlaceholderInStubFile()
    {
        //given
        $classStub = new ClassStub();

        //when
        $classStub->replacePlaceholders(['class' => 'TestClassName', 'fields' => 'fieldA, fieldB']);

        //then
        $this->assertContains('TestClassName', $classStub->contents());
        $this->assertContains('fieldA, fieldB', $classStub->contents());
    }

    /**
     * @test
     */
    public function shouldAddPropertyWithType()
    {
        //given
        $classStub = new ClassStub();

        //when
        $classStub->addColumn(new DatabaseColumn('test_field', 'string'));

        //then
        $this->assertContains('test_field', $classStub->contents());
    }

    /**
     * @test
     */
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
        $this->assertNotRegExp('/{(\w*)}/', $classStub->contents());
    }

    /**
     * @test
     */
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
        $this->assertContains("'field7', \n", $classStub->getFieldsAsString());
    }

    /**
     * @test
     */
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
