<?php


namespace Ouzo\Routing\Loader;


interface Loader
{
    /**
     * @param array $resources
     * @return RouteMetadataCollection
     */
    public function load(array $resources): RouteMetadataCollection;
}