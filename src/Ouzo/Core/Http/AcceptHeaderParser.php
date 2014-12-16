<?php
namespace Ouzo\Http;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

class AcceptHeaderParser
{
    public static function parse($data)
    {
        $array = array();
        $items = Arrays::filterNotBlank(explode(',', $data));
        foreach ($items as $item) {
            $elements = explode(';', $item);
            $media = Arrays::first($elements);
            $params = array_slice($elements, 1);

            list($type, $subtype) = Arrays::map(explode('/', $media), Functions::trim());
            $q = Arrays::getValue(self::extractParams($params), 'q');
            $array[] = array('type' => $type, 'subtype' => $subtype, 'q' => $q);
        }
        usort($array, '\Ouzo\Http\AcceptHeaderParser::_compare');
        return Arrays::toMap($array, function ($input) {
            return $input['type'] . '/' . $input['subtype'];
        }, function ($input) {
            return $input['q'];
        });
    }

    public static function _compare($a, $b)
    {
        $a_q = floatval(Arrays::getValue($a, 'q', 1));
        $b_q = floatval(Arrays::getValue($b, 'q', 1));
        if ($a_q === $b_q) {
            if ($r = self::_compareSubType($a['type'], $b['type'])) {
                return $r;
            } else {
                return self::_compareSubType($a['subtype'], $b['subtype']);
            }
        } else {
            return $a_q < $b_q;
        }
    }

    public static function _compareSubType($a, $b)
    {
        if ($a === '*' && $b !== '*') {
            return 1;
        } elseif ($b === '*' && $a !== '*') {
            return -1;
        } else {
            return 0;
        }
    }

    private static function extractParams($elements)
    {
        $params = array();
        foreach ($elements as $element) {
            list($name, $value) = Arrays::map(explode('=', $element), Functions::trim());
            $params[$name] = $value;
        }
        return $params;
    }
}
