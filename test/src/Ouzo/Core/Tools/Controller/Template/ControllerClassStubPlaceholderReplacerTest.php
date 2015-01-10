<?php
use Ouzo\Tests\Assert;
use Ouzo\Tools\Controller\Template\Generator;

class ControllerClassStubPlaceholderReplacerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReplaceClassNameAndNamespace()
    {
        //given
        $generator = new Generator('users');

        //when
        $templateContents = $generator->templateContents();

        //then
        Assert::thatString($templateContents)
            ->contains('namespace \Application\Controller;')
            ->contains('class UsersController extends Controller');
    }
}
