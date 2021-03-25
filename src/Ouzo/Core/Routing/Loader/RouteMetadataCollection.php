<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing\Loader;

use Ouzo\Utilities\Comparator;
use Ouzo\Utilities\FluentArray;

class RouteMetadataCollection
{
    /** @var RouteMetadata[] */
    private array $elements;

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

    public function sort(): RouteMetadataCollection
    {
        $elementsWithoutParameters = FluentArray::from($this->elements)
            ->filter(fn(RouteMetadata $route) => !$route->hasParameters())
            ->sort(Comparator::compareBy('getUri()', 'getHttpMethod()'))
            ->toArray();

        $elementsWithParameters = FluentArray::from($this->elements)
            ->filter(fn(RouteMetadata $route) => $route->hasParameters())
            ->sort(Comparator::compareBy('getUri()', 'getHttpMethod()'))
            ->toArray();
        $this->elements = array_values(array_merge($elementsWithoutParameters, $elementsWithParameters));

        return $this;
    }

    /** @return RouteMetadata[] */
    public function toArray(): array
    {
        return $this->elements;
    }
}
