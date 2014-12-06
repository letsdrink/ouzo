<?php
namespace Ouzo;

use InvalidArgumentException;
use Ouzo\Utilities\Arrays;

class SessionObject
{
    public function has($keys)
    {
        return Arrays::hasNestedKey($_SESSION, Arrays::toArray($keys));
    }

    public function get($keys)
    {
        if (!isset($_SESSION)) {
            return null;
        }
        return Arrays::getNestedValue($_SESSION, $keys);
    }

    public function set()
    {
        if (!isset($_SESSION)) {
            return null;
        }
        list($keys, $value) = $this->getKeyAndValueArguments(func_get_args());

        Arrays::setNestedValue($_SESSION, $keys, $value);
        return $this;
    }

    public function flush()
    {
        unset($_SESSION);
        return $this;
    }

    public function all()
    {
        return isset($_SESSION) ? $_SESSION : null;
    }

    public function remove($keys)
    {
        if (!isset($_SESSION)) {
            return null;
        }
        Arrays::removeNestedKey($_SESSION, Arrays::toArray($keys));
    }

    public function push($args)
    {
        if (!isset($_SESSION)) {
            return null;
        }
        list($keys, $value) = $this->getKeyAndValueArguments(func_get_args());

        $array = $this->get($keys) ? : array();
        $array[] = $value;
        Arrays::setNestedValue($_SESSION, $keys, $array);
    }

    private function getKeyAndValueArguments($args)
    {
        if (count($args) == 1 && is_array($args[0])) {
            $args = $args[0];
        }
        if (count($args) < 2) {
            throw new InvalidArgumentException('Method needs at least two arguments: key and value');
        }

        $value = array_pop($args);
        $keys = Arrays::toArray($args);
        return array($keys, $value);
    }
}
