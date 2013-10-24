<?php
namespace Ouzo;

use InvalidArgumentException;

class ControllerUrl
{
    public static function createUrl($params)
    {
        if (is_string($params)) {
            return self::_createUrlFromString($params);
        } else if (is_array($params)) {
            return self::_createUrlFromArray($params);
        }
        throw new InvalidArgumentException('Illegal arguments');
    }

    private static function _createUrlFromString($params)
    {
        if (empty($params)) {
            throw new InvalidArgumentException("String argument can't be empty");
        }
        $defaults = Config::getValue('global');
        return $defaults['prefix_system'] . $params;
    }

    private static function _createUrlFromArray($params)
    {
        $defaults = Config::getValue('global');
        if (!empty($params['controller']) && !empty($params['action'])) {
            $returnUrl = $defaults['prefix_system'];
            $returnUrl .= '/' . $params['controller'];
            $returnUrl .= '/' . $params['action'];

            if (!empty($params['extraParams'])) {
                $returnUrl .= self::_mergeParams($params['extraParams']);
            }
            return $returnUrl;
        }
        if (!empty($params['string'])) {
            return $defaults['prefix_system'] . $params['string'];
        }
        throw new InvalidArgumentException('Illegal arguments');
    }

    private static function _mergeParams(array $params)
    {
        $merged = '';
        foreach ($params as $param => $value) {
            $merged .= '/' . $param . '/' . $value;
        }
        return $merged;
    }
}