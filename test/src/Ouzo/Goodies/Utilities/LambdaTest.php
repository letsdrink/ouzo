<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Model;
use Ouzo\Tests\Assert;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Lambda;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LambdaTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetId()
    {
        // given
        $object = $this->mockModel(14);
        $function = Lambda::id();

        //when
        $result = Functions::call($function, $object);

        //then
        Assert::that($result)->isEqualTo(14);
    }

    /**
     * @test
     */
    public function shouldBindMethodParameters()
    {
        //given
        $object = new ExtractorTestClass();
        $function = Lambda::returnArgument('argument');

        //when
        $result = Functions::call($function, $object);

        //then
        Assert::thatString($result)->isEqualTo('argument');
    }

    /**
     * @test
     */
    public function shouldExtractFieldAfterMethod()
    {
        //given
        $product = new ExtractorTestClass();
        $object = (object)['name' => 'category'];

        $function = Lambda::returnArgument($object)->name;

        //when
        $result = Functions::call($function, $product);

        //then
        Assert::thatString($result)->isEqualTo('category');
    }

    private function mockModel(int $id): Model
    {
        /** @var Model|MockObject $model */
        $model = $this->createMock(Model::class);
        $model->method('getId')->willReturn($id);
        return $model;
    }
}
