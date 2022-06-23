<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing\Annotation;

use Attribute;
use Ouzo\Config\ConfigValueSelector;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        private string $path,
        private array $httpMethods,
        private ?int $httpResponseCode,
    )
    {
    }

    public function getPath(): string
    {
        $pathFromConfig = ConfigValueSelector::selectConfigValue($this->path);
        return is_null($pathFromConfig) ? $this->path : $pathFromConfig;
    }

    public function getHttpMethods(): array
    {
        return $this->httpMethods;
    }

    public function getHttpResponseCode(): ?int
    {
        return $this->httpResponseCode;
    }
}
