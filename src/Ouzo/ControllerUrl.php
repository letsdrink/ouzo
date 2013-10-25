<?php
namespace Ouzo;

use InvalidArgumentException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Joiner;

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
        $prefixSystem = $defaults['prefix_system'];

        $controller = Arrays::getValue($params, 'controller');
        $action = Arrays::getValue($params, 'action');
        $extraParams = Arrays::getValue($params, 'extraParams');
        if ($controller && $action) {
            $returnUrl = Joiner::on('/')->join(array($prefixSystem, $controller, $action));
            if ($extraParams) {
                $returnUrl .= self::_mergeParams($extraParams);
            }
            return $returnUrl;
        }

        $string = Arrays::getValue($params, 'string');
        if ($string) {
            return $prefixSystem . $string;
        }
        throw new InvalidArgumentException('Illegal arguments');
    }

    private static function _mergeParams(array $params)
    {
        return '/' . Joiner::on('/')->map(function ($key, $value) {
            return $key . '/' . $value;
        })->join($params);
    }
}