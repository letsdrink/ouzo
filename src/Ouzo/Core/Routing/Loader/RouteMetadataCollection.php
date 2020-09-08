<?php

namespace Ouzo\Routing\Loader;

use Ouzo\Utilities\Comparator;
use Ouzo\Utilities\FluentArray;

class RouteMetadataCollection
{
    /** @var RouteMetadata[] */
    private $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    public function addRouteMetadata(RouteMetadata ...$routeMetadata): void
    {
        foreach ($routeMetadata as $metadata) {
            $this->elements[] = $metadata;
        }
    }

    public function addCollection(RouteMetadataCollection $collection): void
    {
        $this->elements = array_values(array_merge($this->elements, $collection->toArray()));
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function sort(): self
    {
        $elementsWithoutParameters = FluentArray::from($this->elements)
            ->filter(function (RouteMetadata $route) {
                return !$route->hasParameters();
            })
            ->sort(Comparator::compareBy('getUri()', 'getMethod()'))
            ->toArray();

        $elementsWithParameters = FluentArray::from($this->elements)
            ->filter(function (RouteMetadata $route) {
                return $route->hasParameters();
            })
            ->sort(Comparator::compareBy('getUri()', 'getMethod()'))
            ->toArray();
        $this->elements = array_values(array_merge($elementsWithoutParameters, $elementsWithParameters));
        return $this;
    }

    /**
     * @return RouteMetadata[]
     */
    public function toArray(): array
    {
        return $this->elements;
    }

}