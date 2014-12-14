<?php

namespace Ouzo\Tools\Model\Template;

use Ouzo\Tests\Assert;
use Ouzo\Utilities\Path;

class ClassPathResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldFindFileNameForClassNameAndNamespace()
    {
        //given
        $resolver = ClassPathResolver::forClassAndNamespace('UserAcl', 'Model/My/Name/Space');

        //when
        $classPath = $resolver->getClassFileName();

        //then
        Assert::thatString($classPath)->endsWith(Path::join('application', 'model', 'my', 'name', 'space', 'UserAcl.php'));
    }
}
 