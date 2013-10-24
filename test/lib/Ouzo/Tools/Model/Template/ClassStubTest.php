<?php


namespace Ouzo\Tools;


use Ouzo\Tools\Model\Template\ClassStub;

class ClassStubTest extends \PHPUnit_Framework_TestCase {

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
        $classStub->addFiled('test_field', 'string');

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
            ->addPlaceholderReplacement('sequence', 'sequence')
            ->addPlaceholderReplacement('primary', 'primary')
            ->addPlaceholderReplacement('table', 'table');

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
        ->addFiled('field1', 'string')
        ->addFiled('field2', 'string')
        ->addFiled('field3', 'string')
        ->addFiled('field4', 'string')
        ->addFiled('field5', 'string')
        ->addFiled('field6', 'string')
        ->addFiled('field7', 'string')
        ->addFiled('field8', 'string')
        ->addFiled('field9', 'string');


        //then
        $this->assertContains('sssssaweqwes', $classStub->contents());
    }
}
 