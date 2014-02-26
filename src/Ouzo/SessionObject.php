<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class SessionObject
{

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function get($key)
    {
        return $this->has($key) ? $_SESSION[$key] : null;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
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

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function push($key, $value)
    {
        $array = $this->get($key) ?: array();
        $array[] = $value;
        $this->set($key, $array);
    }
}