<?php

use Ouzo\Routing\Loader\RouteMetadata;
use Ouzo\Routing\Loader\RouteMetadataCollection;
use PHPUnit\Framework\TestCase;

class RouteMetadataCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldAddRouteMetadata()
    {
        //given
        $collection1 = new RouteMetadataCollection();

        //when
        $collection1->addRouteMetadata(new RouteMetadata('', '', '', ''));
        $collection1->addRouteMetadata(new RouteMetadata('', '', '', ''));

        //then
        $this->assertEquals(2, $collection1->count());

        //given
        $collection2 = new RouteMetadataCollection();

        //when
        $collection2->addCollection($collection1);

        //then
        $this->assertEquals(2, $collection2->count());
    }

    /**
     * @test
     */
    public function shouldPutRoutesWithParametersToEndOfArray()
    {
        //given
        $collection = new RouteMetadataCollection([
            new RouteMetadata('/a', '', '', ''),
            new RouteMetadata('/test2', '', '', ''),
            new RouteMetadata('/a/:id', '', '', ''),
            new RouteMetadata('/b/:id', '', '', ''),
            new RouteMetadata('/test/:id', '', '', ''),
            new RouteMetadata('/test', '', '', ''),
        ]);

        //when
        $elements = $collection->routesWithParametersToBottom()->toArray();

        //then
        $this->assertEquals('/a', $elements[0]->getUri());
        $this->assertEquals('/test2', $elements[1]->getUri());
        $this->assertEquals('/test', $elements[2]->getUri());
        $this->assertEquals('/a/:id', $elements[3]->getUri());
        $this->assertEquals('/b/:id', $elements[4]->getUri());
        $this->assertEquals('/test/:id', $elements[5]->getUri());
    }

}