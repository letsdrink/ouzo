<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

class Binder
{
    private string $scope = Scope::PROTOTYPE;
    private bool $eager = false;
    private array $boundClassNames = [];
    private ?object $instance = null;
    private ?string $factoryClassName = null;

    public function __construct(
        private string $className,
        private string $name = ''
    )
    {
    }

    public function in(string $scope): static
    {
        $this->scope = $scope;
        return $this;
    }

    public function asEagerSingleton(): static
    {
        $this->scope = Scope::SINGLETON;
        $this->eager = true;
        return $this;
    }

    public function to(string...$boundClassNames): static
    {
        $this->boundClassNames = $boundClassNames;
        return $this;
    }

    public function toInstance(object $instance): static
    {
        $this->instance = $instance;
        return $this;
    }

    public function throughFactory(string $factoryClassName): static
    {
        $this->factoryClassName = $factoryClassName;
        return $this;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function isEager(): bool
    {
        return $this->eager;
    }

    public function getBoundClassNames(): array
    {
        return $this->boundClassNames;
    }

    public function getInstance(): ?object
    {
        return $this->instance;
    }

    public function getFactoryClassName(): ?string
    {
        return $this->factoryClassName;
    }
}
