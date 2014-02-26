<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class NamespacedSession
{
    private $_namespace;

    public function __construct($namespace)
    {
        $this->_namespace = $namespace;
    }

    public function has($key)
    {
        return isset($_SESSION[$this->_namespace][$key]);
    }

    public function get($key)
    {
        return $this->has($key) ? $_SESSION[$this->_namespace][$key] : null;
    }

    public function set($key, $value)
    {
        $_SESSION[$this->_namespace][$key] = $value;
        return $this;
    }

    public function flush()
    {
        unset($_SESSION[$this->_namespace]);
        return $this;
    }

    public function all()
    {
        return Arrays::getValue($_SESSION, $this->_namespace, array());
    }

    public function remove($key)
    {
        unset($_SESSION[$this->_namespace][$key]);
    }
}