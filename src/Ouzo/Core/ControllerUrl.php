<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use InvalidArgumentException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Joiner;

class ControllerUrl
{
    /**
     * @param string|array $params
     * @return string
     * @throws InvalidArgumentException
     */
    public static function createUrl($params)
    {
        if (is_string($params)) {
            return self::createUrlFromString($params);
        }
        if (is_array($params)) {
            return self::createUrlFromArray($params);
        }
        throw new InvalidArgumentException('Illegal arguments');
    }

    /**
     * @param string $params
     * @return string
     * @throws InvalidArgumentException
     */
    private static function createUrlFromString($params)
    {
        if (empty($params)) {
            throw new InvalidArgumentException("String argument can't be empty");
        }
        return Config::getValue('global', 'prefix_system') . $params;
    }

    /**
     * @param array $params
     * @return string
     * @throws InvalidArgumentException
     */
    private static function createUrlFromArray($params)
    {
        $prefixSystem = Config::getValue('global', 'prefix_system');

        $controller = Arrays::getValue($params, 'controller');
        $action = Arrays::getValue($params, 'action');
        $extraParams = Arrays::getValue($params, 'extraParams');
        if ($controller && $action) {
            $url = Joiner::on('/')->join([$prefixSystem, $controller, $action]);
            if ($extraParams) {
                $url .= self::mergeParams($extraParams);
            }
            return $url;
        }

        $string = Arrays::getValue($params, 'string');
        if ($string) {
            return $prefixSystem . $string;
        }
        throw new InvalidArgumentException('Illegal arguments');
    }

    /**
     * @param array $params
     * @return string
     */
    private static function mergeParams(array $params)
    {
        return '/' . Joiner::on('/')->map(function ($key, $value) {
            return $key . '/' . $value;
        })->join($params);
    }
}
