<?php
namespace Ouzo;

use Exception;
use Ouzo\Api\InternalException;
use Ouzo\Uri\PathProvider;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Json;
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
            $paramsGet = strpos($params, '&') ? str_replace('?', '', (strstr($params, '?') ?: $params)) : '';
            $paramsUrl = strstr($params, '?', true) ?: $params;
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
        $parseUrl = parse_url($this->_pathProvider->getPath(), PHP_URL_PATH) ?: '/';
        return $this->_removeDuplicatedSlashes($parseUrl);
    }

    private function _removeDuplicatedSlashes($parseUrl)
    {
        return preg_replace('#/{2,}#', '/', $parseUrl);
    }

    public function getPathWithoutPrefix()
    {
        $prefix = Config::getValue('global', 'prefix_system');
        $path = Strings::removePrefix($this->getPath(), $prefix);
        if (preg_match('#.+/$#', $path)) {
            $path = rtrim($path, '/');
        }
        return $path;
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
        if ($path != null) {
            $prefix = Config::getValue('global', 'prefix_system');
            $pathWithoutPrefix = urldecode(str_replace($prefix, '', $path));
            return preg_split('#/|\?#', $pathWithoutPrefix, $limit, PREG_SPLIT_NO_EMPTY);
        }
        return array();
    }

    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public static function getRequestType()
    {
        return Arrays::getValue($_POST, '_method', $_SERVER['REQUEST_METHOD']);
    }

    public static function getRequestParameters($stream = 'php://input')
    {
        $parameters = self::_parseRequest(stream_get_contents(fopen($stream, 'r')));
        return Arrays::toArray($parameters);
    }

    private static function _parseRequest($content)
    {
        if (Strings::equal(ContentType::value(), 'application/json')) {
            if (!Json::isJson($content)) {
                throw new InternalException('JSON string is malformed');
            }
            return Json::decode($content, true);
        }
        parse_str($content, $parameters);
        return $parameters;
    }

    public static function addPrefixIfNeeded($url)
    {
        $prefix = Config::getValue('global', 'prefix_system');
        $url = Strings::removePrefix($url, $prefix);
        return $prefix . $url;
    }

    public static function getProtocol()
    {
        return (
            self::_isServerVariableSetAndHasValue('HTTPS', array('on', 1)) ||
            self::_isServerVariableSetAndHasValue('HTTP_X_FORWARDED_PROTO', 'https')
        ) ? 'https://' : 'http://';
    }

    private static function _isServerVariableSetAndHasValue($variableName, $values)
    {
        $value = Arrays::getValue($_SERVER, $variableName);
        return in_array($value, Arrays::toArray($values));
    }

    public static function getHost()
    {
        return Arrays::getValue($_SERVER, 'HTTP_HOST');
    }
}