<?php
namespace Thulium;

class Loader
{
    private $_includePath = array();

    private $_classPath = array();

    public function setIncludePath($path)
    {
        $this->_includePath[] = $path;
        return $this;
    }

    public function getIncludePath()
    {
        return $this->_includePath;
    }

    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
        return $this;
    }

    public function loadClass($className)
    {
        $filePath = '';

        if (empty($this->_classPath[$className])) {
            $class = $className;
            $lastPosition = strripos($className, '\\');

            if ($lastPosition != null) {
                $namespace = strtolower(substr($class, 0, $lastPosition));
                $class = substr($class, $lastPosition + 1);

                $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }

            $filePath .= str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        }

        foreach ($this->_includePath as $key) {
            if (file_exists(ROOT_PATH . $key . $filePath)) {
                /** @noinspection PhpIncludeInspection */
                require_once(ROOT_PATH . $key . $filePath);
                return true;
            }
        }

        return $this;
    }
}