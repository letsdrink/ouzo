<?php
use Ouzo\Tests\Assert;
use Ouzo\Tools\Controller\Template\ActionGenerator;

class ActionGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnReplacedTemplateContents()
    {
        //given
        $actionGenerator = new ActionGenerator('index');

        //when
        $templateContents = $actionGenerator->templateContents();

        //then
        Assert::thatString($templateContents)
            ->contains('public function index');
    }
}
