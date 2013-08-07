<?php
namespace Thulium;

use InvalidArgumentException;

class ControllerUrl
{
    public static function createUrl(array $params)
    {
        $defaults = Config::load()->getConfig('global');
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