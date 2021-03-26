<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use InvalidArgumentException;
use Ouzo\Utilities\Arrays;

class SessionObject
{
    public function has(string...$keys): bool
    {
        return Arrays::hasNestedKey($_SESSION, Arrays::toArray($keys));
    }

    public function get(string...$keys): mixed
    {
        if (!isset($_SESSION)) {
            return null;
        }
        return Arrays::getNestedValue($_SESSION, $keys);
    }

    public function set(mixed...$args): static
    {
        if (!isset($_SESSION)) {
            return $this;
        }
        [$keys, $value] = $this->getKeyAndValueArguments($args);

        Arrays::setNestedValue($_SESSION, $keys, $value);
        return $this;
    }

    public function flush(): static
    {
        unset($_SESSION);
        return $this;
    }

    public function all(): ?array
    {
        return isset($_SESSION) ? $_SESSION : null;
    }

    public function remove($keys): void
    {
        if (!isset($_SESSION)) {
            return;
        }
        Arrays::removeNestedKey($_SESSION, Arrays::toArray($keys));
    }

    public function push(mixed...$args): void
    {
        if (!isset($_SESSION)) {
            return;
        }
        [$keys, $value] = $this->getKeyAndValueArguments($args);

        $array = $this->get(...$keys) ?: [];
        $array[] = $value;
        Arrays::setNestedValue($_SESSION, $keys, $array);
    }

    private function getKeyAndValueArguments(array $args): array
    {
        if (count($args) == 1 && is_array($args[0])) {
            $args = $args[0];
        }
        if (count($args) < 2) {
            throw new InvalidArgumentException('Method needs at least two arguments: key and value');
        }

        $value = array_pop($args);
        $keys = Arrays::toArray($args);
        return [$keys, $value];
    }
}
