<?php
namespace Thulium;

use Exception;
use Thulium\Utilities\Functions;

class Session
{
    private $_sessionNamespace;

    public function __construct($namespace)
    {
        $this->_sessionNamespace = $namespace;
    }

    static public function startSession()
    {
        if (!empty($_SERVER["REQUEST_URI"])) {
            $config = Config::load()->getConfig('global');
            $path = "/tmp" . str_replace('panel2.0', '', $config['prefix_system']) . "thulium_sess";

            if (!is_dir($path)) {
                mkdir($path, 0700, true);
            }

            session_save_path($path);

            if (session_id() == '') {
                session_start();
            }
        }
    }

    static public function runWithoutSession($callable)
    {
        $sessionIsOpened = (session_id() != '');
        if ($sessionIsOpened) {
            session_write_close();
        }
        try {
            return Functions::call($callable, null);
        } catch (Exception $e) {
            if ($sessionIsOpened) {
                session_start();
            }
            throw $e;
        }
    }

    public function get($key)
    {
        $sessionValue = null;

        if (isset($_SESSION[$this->_sessionNamespace][$key])) {
            $sessionValue = $_SESSION[$this->_sessionNamespace][$key];
        }

        return (!empty($sessionValue) ? $sessionValue : null);
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
}