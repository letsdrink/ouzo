<?php
namespace Ouzo;

use Ouzo\Uri\PathProvider;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Uri
{
    private $_pathProvider;

    public function __construct($pathProvider = null)
    {
        $this->_pathProvider = $pathProvider == null ? new PathProvider() : $pathProvider;
    }

    public function getParams()
    {
        $path = $this->_pathProvider->getPath();
        $pathElements = $this->_parsePath($path, 3);
        return $this->_splitParamsKeyValueMap($pathElements);
    }

    private function _splitParamsKeyValueMap($pathElements)
    {
        $paramsArray = array();
        if (!empty($pathElements[2])) {
            $params = $pathElements[2];
            $paramsGet = strpos($params, '&') ? str_replace('?', '', (strstr($params, '?') ? : $params)) : '';
            $paramsUrl = strstr($params, '?', true) ? : $params;
            parse_str($paramsGet, $parsedParamsGet);
            $paramsArray = array_merge($paramsArray, $this->_parseParams($paramsUrl), $parsedParamsGet);
        }
        return $paramsArray;
    }

    private function _parseParams($params)
    {
        $paramsArray = array();
        $paramsParts = explode('/', $params);
        $k = 0;
        for ($i = 0; $i < (int)floor((count($paramsParts) / 2)); $i++) {
            $tmpKey = $paramsParts[$k];
            $tmpValue = $paramsParts[$k + 1];
            if (!empty($tmpValue)) {
                $paramsArray[$tmpKey] = $tmpValue;
            }
            $k = $k + 2;
        }
        return $paramsArray;
    }

    public function getPath()
    {
        return parse_url($this->_pathProvider->getPath(), PHP_URL_PATH);
    }

    public function getPathWithoutPrefix()
    {
        $defaults = Config::getValue('global');
        return Strings::removePrefix($this->getPath(), $defaults['prefix_system']);
    }

    public function getParam($param)
    {
        $params = $this->getParams();
        return Arrays::getValue($params, $param);
    }

    public function getRawController()
    {
        $path = $this->_pathProvider->getPath();
        $pathElements = $this->_parsePath($path);
        return Arrays::firstOrNull($pathElements);
    }

    public function getController()
    {
        $rawController = $this->getRawController();
        return $rawController ? Strings::underscoreToCamelCase($rawController) : null;
    }

    public function getAction()
    {
        $path = $this->_pathProvider->getPath();
        $pathElements = $this->_parsePath($path);
        return Arrays::getValue($pathElements, 1);
    }

    private function _parsePath($path = null, $limit = null)
    {
        $parsedPath = null;
        $prefixSystem = Config::getValue('global');
        if ($path != null) {
            $pathWithoutPrefix = urldecode(str_replace($prefixSystem['prefix_system'], '', $path));
            $parsedPath = preg_split('#/|\?#', $pathWithoutPrefix, $limit, PREG_SPLIT_NO_EMPTY);
        }
        return $parsedPath;
    }

    public static function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public static function getRequestType()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}