<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Http\HttpMethod;
use Ouzo\Routing\Loader\RouteMetadata;
use Ouzo\Routing\Loader\RouteMetadataCollection;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RouteMetadataCollectionTest extends TestCase
{
    #[Test]
    public function shouldAddRouteMetadata()
    {
        //given
        $collection1 = new RouteMetadataCollection();

        //when
        $collection1->addRouteMetadata(new RouteMetadata('', '', '', '', null));
        $collection1->addRouteMetadata(new RouteMetadata('', '', '', '', null));

        //then
        $this->assertEquals(2, $collection1->count());

        //given
        $collection2 = new RouteMetadataCollection();

        //when
        $collection2->addCollection($collection1);

        //then
        $this->assertEquals(2, $collection2->count());
    }

    #[Test]
    public function shouldSortRoutesWithParametersAtBottomOfArray()
    {
        //given
        $collection = new RouteMetadataCollection([
            new RouteMetadata('/test2', '', '', '', null),
            new RouteMetadata('/a', '', '', '', null),
            new RouteMetadata('/a/:id', '', '', '', null),
            new RouteMetadata('/test/:id', '', '', '', null),
            new RouteMetadata('/b/:id', '', '', '', null),
            new RouteMetadata('/test', '', '', '', null),
            new RouteMetadata('/a/b', '', '', '', null),
            new RouteMetadata('/a/:id/b', '', '', '', null),
            new RouteMetadata('/c/:id/d/:other_id', '', '', '', null),
            new RouteMetadata('/c/:id/d/e', '', '', '', null),
        ]);

        //when
        $elements = $collection->sort()->toArray();

        //then
        Assert::thatArray($elements)->onMethod('getUri')->containsExactly(
            '/a',
            '/a/b',
            '/test',
            '/test2',
            '/a/:id',
            '/a/:id/b',
            '/b/:id',
            '/c/:id/d/e',
            '/c/:id/d/:other_id',
            '/test/:id',
        );
    }

    #[Test]
    public function shouldSortRoutesWithTheSameUrisAndDifferentMethods()
    {
        //given
        $collection = new RouteMetadataCollection([
            new RouteMetadata('/test', 'POST', '', '', null),
            new RouteMetadata('/test', 'GET', '', '', null),
        ]);

        //when
        $elements = $collection->sort()->toArray();

        //then
        Assert::thatArray($elements)->onMethod('getUri')->containsExactly('/test', '/test');
        Assert::thatArray($elements)->onMethod('getHttpMethod')->containsExactly(HttpMethod::GET, HttpMethod::POST);
    }
}
