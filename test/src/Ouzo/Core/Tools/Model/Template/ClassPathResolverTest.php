<?php
use Ouzo\Tests\Assert;
use Ouzo\Tools\Model\Template\ClassPathResolver;
use Ouzo\Utilities\Path;

class ClassPathResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldFindFileNameForClassNameAndNamespace()
    {
        //given
        $resolver = ClassPathResolver::forClassAndNamespace('UserAcl', '\\Application\\Model\\My\\Name\\Space');

        //when
        $classPath = $resolver->getClassFileName();

        //then
        Assert::thatString($classPath)->endsWith(Path::join('Application', 'Model', 'My', 'Name', 'Space', 'UserAcl.php'));
    }

    /**
     * @test
     */
    public function shouldResolveFilePathForDefaultNamespaces()
    {
        //given
        $resolver = ClassPathResolver::forClassAndNamespace('UserAcl', '\\Application\\Model');

        //when
        $classPath = $resolver->getClassFileName();

        //then
        Assert::thatString($classPath)->endsWith(Path::join('Application', 'Model', 'UserAcl.php'));
    }
}
