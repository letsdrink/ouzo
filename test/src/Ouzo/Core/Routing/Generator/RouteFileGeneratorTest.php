<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Doctrine\Common\Annotations\AnnotationReader;
use Ouzo\Routing\Generator\RouteFileGenerator;
use Ouzo\Routing\Loader\AnnotationClassLoader;
use Ouzo\Routing\Loader\AnnotationDirectoryLoader;
use Ouzo\Utilities\Path;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RouteFileGeneratorTest extends TestCase
{
    #[Test]
    public function shouldGenerateRouteFileTemplate()
    {
        //given
        $reader = new AnnotationReader();
        $classLoader = new AnnotationClassLoader($reader);
        $directoryLoader = new AnnotationDirectoryLoader($classLoader);
        $routeFileGenerator = new RouteFileGenerator($directoryLoader);
        $path = Path::joinWithTemp('GeneratedRoutes.php');

        //when
        $result = $routeFileGenerator->generate($path, [__DIR__ . '/../Fixtures/Annotation']);

        //then
        $this->assertIsInt($result);
        $this->assertEquals($result, strlen(file_get_contents($path)));
    }
}
