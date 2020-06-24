<?php

use Application\Model\Test\CrudController;
use Application\Model\Test\FooClass;
use Application\Model\Test\MultipleMethods;
use Application\Model\Test\SimpleController;
use Doctrine\Common\Annotations\AnnotationReader;
use Ouzo\Routing\Loader\AnnotationClassLoader;
use Ouzo\Routing\Loader\AnnotationDirectoryLoader;
use Ouzo\Routing\Loader\RouteMetadataCollection;
use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;
use PHPUnit\Framework\TestCase;

class AnnotationDirectoryLoaderTest extends TestCase
{
    private $loader;
    private $annotationClassLoader;

    public function setUp(): void
    {
        parent::setUp();
        AnnotationReader::addGlobalIgnoredName('test');
        AnnotationReader::addGlobalIgnoredName('dataProvider');
        $this->annotationClassLoader = Mock::mock(AnnotationClassLoader::class);
        Mock::when($this->annotationClassLoader)->load(Mock::anyArgList())->thenReturn(new RouteMetadataCollection());
        $this->loader = new AnnotationDirectoryLoader($this->annotationClassLoader);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenNonExistentDirectoryPath()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load(['invalid_directory']);
    }

    /**
     * @test
     */
    public function shouldResolveDirectoryPath()
    {
        $paths = $this->loader->resolvePaths([__DIR__ . '/../Fixtures/']);

        Assert::thatArray($paths)->containsExactly(
            __DIR__ . '/../Fixtures/'
        );
    }

    /**
     * @test
     */
    public function shouldResolveGlobPath()
    {
        $paths = $this->loader->resolvePaths([__DIR__ . '/../Fixtures/*tion']);

        Assert::thatArray($paths)->containsExactly(
            __DIR__ . '/../Fixtures/Annotation'
        );
    }

    /**
     * @test
     */
    public function shouldLoadClassesInDirectory()
    {
        $this->loader->load([__DIR__ . '/../Fixtures/Annotation']);

        Mock::verify($this->annotationClassLoader)->neverReceived()->load(FooBar::class);
        Mock::verify($this->annotationClassLoader)
            ->load([CrudController::class])
            ->load([FooClass::class])
            ->load([MultipleMethods::class])
            ->load([SimpleController::class]);
    }
}