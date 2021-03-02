<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Doctrine\Common\Annotations\AnnotationReader;
use Ouzo\Routing\Generator\RouteFileGenerator;
use Ouzo\Routing\Loader\AnnotationClassLoader;
use Ouzo\Routing\Loader\AnnotationDirectoryLoader;
use Ouzo\Tests\Assert;
use Ouzo\Utilities\Path;
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
        $path = Path::joinWithTemp('GeneratedRoutes.php');

        //when
        $result = $routeFileGenerator->generate($path, [__DIR__ . '/../Fixtures/Annotation']);

        //then
        $string = file_get_contents($path);
        $routeFile = <<<GENERATED
<?php

use Ouzo\Routing\Route;

Route::get('/action', \Application\Model\Test\SimpleController::class, 'action');
Route::post('/create', \Application\Model\Test\CrudController::class, 'post');
Route::delete('/delete', \Application\Model\Test\CrudController::class, 'delete');
Route::get('/get', \Application\Model\Test\MultipleMethods::class, 'getAndPost');
Route::post('/post', \Application\Model\Test\MultipleMethods::class, 'getAndPost');
Route::get('/prefix/', \Application\Model\Test\GlobalController::class, 'index');
Route::post('/prefix/action', \Application\Model\Test\GlobalController::class, 'action');
Route::get('/read', \Application\Model\Test\CrudController::class, 'get');
Route::put('/update', \Application\Model\Test\CrudController::class, 'put');

GENERATED;
        $this->assertIsInt($result);
        Assert::thatString($result)->isEqualTo(strlen($string));
        Assert::thatString($string)->isEqualTo($routeFile);
    }
}
