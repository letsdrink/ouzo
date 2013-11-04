<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class Session
{
    private $_sessionNamespace;

    public function __construct($namespace)
    {
        $this->_sessionNamespace = $namespace;
    }

    static public function startSession()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            self::_setSavePath();
            if (session_id() == '') {
                session_start();
            }
        }
    }

    private static function _setSavePath()
    {
        $path = Config::getValue('session', 'path');
        if ($path) {
            if (!is_dir($path)) {
                mkdir($path, 0700, true);
            }
            session_save_path($path);
        }
    }

    public function get($key)
    {
        return Arrays::getValue($this->all(), $key);
    }

    public function set($key, $value)
    {
        $_SESSION[$this->_sessionNamespace][$key] = $value;
        return $this;
    }

    public function push($value)
    {
        $_SESSION[$this->_sessionNamespace][] = $value;
        return $this;
    }

    public function delete()
    {
        unset($_SESSION[$this->_sessionNamespace]);
        return $this;
    }

    public function all()
    {
        return Arrays::getValue($_SESSION, $this->_sessionNamespace, array());
    }
}