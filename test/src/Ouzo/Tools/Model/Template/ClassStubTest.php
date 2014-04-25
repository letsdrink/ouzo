<?php


namespace Ouzo\Tools\Model\Template;

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
        $classStub->replacePlaceholders(array('class' => 'TestClassName', 'fields' => 'fieldA, fieldB'));

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
            ->addPlaceholderReplacement('table_setup', 'table_setup');

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
}
 