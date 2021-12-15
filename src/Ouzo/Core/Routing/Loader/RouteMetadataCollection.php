<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing\Loader;

use Ouzo\Utilities\Comparator;
use Ouzo\Utilities\FluentArray;

class RouteMetadataCollection
{
    private const VERY_END_ALPHABET_VALUE = 'zzzzzzzzzzzzzzzzzzzzz';

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
            ->sort(function (RouteMetadata $lhs, RouteMetadata $rhs) {
                // This moves parameters at the end of list.
                $lshUri = str_replace(':', self::VERY_END_ALPHABET_VALUE, $lhs->getUri());
                $rhsUri = str_replace(':', self::VERY_END_ALPHABET_VALUE, $rhs->getUri());
                $uriComparisonResult = $lshUri <=> $rhsUri;
                return $uriComparisonResult === 0 ? $lhs->getHttpMethod() <=> $rhs->getHttpMethod() : $uriComparisonResult;
            })
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
