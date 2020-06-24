<?php


use Doctrine\Common\Annotations\AnnotationReader;
use Ouzo\Routing\Generator\RouteFileGenerator;
use Ouzo\Routing\Loader\AnnotationClassLoader;
use Ouzo\Routing\Loader\AnnotationDirectoryLoader;
use PHPUnit\Framework\TestCase;

class RouteFileGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGenerateRouteFileTemplate()
    {
        //given
        $reader = new AnnotationReader();
        $classLoader = new AnnotationClassLoader($reader);
        $directoryLoader = new AnnotationDirectoryLoader($classLoader);
        $routeFileGenerator = new RouteFileGenerator($directoryLoader);

        //when
        $result = $routeFileGenerator->generate('/tmp/GeneratedRoutes.php', [__DIR__ . '/../Fixtures/Annotation']);

        //then
        $this->assertIsInt($result);
        $this->assertEquals($result, strlen(file_get_contents('/tmp/GeneratedRoutes.php')));
    }
}