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

}