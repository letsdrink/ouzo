<?php

namespace Ouzo\Routing\Loader;

class RouteMetadataCollection
{
    /** @var RouteMetadata[] */
    private $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    public function addRouteMetadata(RouteMetadata ...$routeMetadata)
    {
        foreach ($routeMetadata as $metadata) {
            $this->elements[] = $metadata;
        }
    }

    public function addCollection(RouteMetadataCollection $collection)
    {
        $this->elements = array_merge($this->elements, $collection->toArray());
    }

    public function count()
    {
        return count($this->elements);
    }

    /**
     * @return RouteMetadata[]
     */
    public function toArray(): array
    {
        return $this->elements;
    }

}