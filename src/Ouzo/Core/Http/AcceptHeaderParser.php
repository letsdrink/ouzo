<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Http;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

class AcceptHeaderParser
{
    public static function parse($data)
    {
        $array = [];
        $items = Arrays::filterNotBlank(explode(',', $data));
        foreach ($items as $item) {
            $elements = explode(';', $item);
            $media = Arrays::first($elements);
            $params = array_slice($elements, 1);

            list($type, $subtype) = self::getTypeAndSubtype($media);
            $q = Arrays::getValue(self::extractParams($params), 'q');
            $array[] = ['type' => $type, 'subtype' => $subtype, 'q' => $q];
        }
        usort($array, [AcceptHeaderParser::class, '_compare']);
        return Arrays::toMap($array, function ($input) {
            return $input['subtype'] ? $input['type'] . '/' . $input['subtype'] : $input['type'];
        }, function ($input) {
            return $input['q'];
        });
    }

    public static function _compare($a, $b): int
    {
        $a_q = floatval(Arrays::getValue($a, 'q', 1));
        $b_q = floatval(Arrays::getValue($b, 'q', 1));
        if ($a_q === $b_q) {
            if ($r = self::_compareSubType($a['type'], $b['type'])) {
                return $r;
            } else {
                return self::_compareSubType($a['subtype'], $b['subtype']);
            }
        }
        return $a_q < $b_q ? 1 : -1;
    }

    public static function _compareSubType($a, $b): int
    {
        if ($a === '*' && $b !== '*') {
            return 1;
        }
        if ($b === '*' && $a !== '*') {
            return -1;
        }
        return 0;
    }

    private static function extractParams($elements)
    {
        $params = [];
        foreach ($elements as $element) {
            list($name, $value) = Arrays::map(explode('=', $element), Functions::trim());
            $params[$name] = $value;
        }
        return $params;
    }

    private static function getTypeAndSubtype($media): array
    {
        $result = Arrays::map(explode('/', $media), Functions::trim());
        return [
            Arrays::getValue($result, 0),
            Arrays::getValue($result, 1)
        ];
    }
}
