<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Assert;
use Ouzo\Tools\Utils\ClassPathResolver;
use Ouzo\Utilities\Path;

use PHPUnit\Framework\TestCase; 

class ClassPathResolverTest extends TestCase
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

    /**
     * @test
     */
    public function shouldResolveDirectoryPath()
    {
        //given
        $resolver = ClassPathResolver::forClassAndNamespace('UserAcl', '\\Application\\View');

        //when
        $directoryPath = $resolver->getClassDirectory();

        //then
        Assert::thatString($directoryPath)->endsWith(Path::join('Application', 'View', 'UserAcl'));
    }
}
