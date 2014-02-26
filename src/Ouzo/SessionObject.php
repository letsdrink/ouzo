<?php
namespace Ouzo;

use InvalidArgumentException;
use Ouzo\Utilities\Arrays;

class SessionObject
{

    // TODO add nested has
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function get($args)
    {
        if (!isset($_SESSION)) {
            return null;
        }

        $value = $_SESSION;
        foreach ($args as $arg) {
            $value = Arrays::getValue($value, $arg);
            if (!$value) {
                return null;
            }
        }
        return $value;
    }

    public function set()
    {
        $args = func_get_args();
        if (count($args) == 1 && is_array($args[0])) {
            $args = $args[0];
        }
        if (count($args) < 2) {
            throw new InvalidArgumentException('Method needs at least two arguments: key and value');
        }

        $value = array_pop($args);
        $keys = Arrays::toArray($args);
        return $this->_set($keys, $value);
    }

    // TODO move to Arrays
    private function _set($keys, $value)
    {
        $session = & $_SESSION;
        foreach ($keys as $key) {
            if (!isset($session[$key])) {
                $session[$key] = array();
            }
            $session = & $session[$key];
        }
        $session = $value;
        return $this;
    }

    public function flush()
    {
        unset($_SESSION);
        return $this;
    }

    public function all()
    {
        return $_SESSION;
    }

    // TODO add nested remove
    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    // TODO remove duplicated code
    public function push($args)
    {
        $args = func_get_args();
        if (count($args) == 1 && is_array($args[0])) {
            $args = $args[0];
        }
        if (count($args) < 2) {
            throw new InvalidArgumentException('Method needs at least two arguments: key and value');
        }

        $value = array_pop($args);
        $keys = Arrays::toArray($args);

        $array = $this->get($keys) ? : array();
        $array[] = $value;
        $this->_set($keys, $array);
    }

}