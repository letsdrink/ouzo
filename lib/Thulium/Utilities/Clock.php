<?php
namespace Thulium\Utilities;

use DateTime;

class Clock
{
    static $freeze = false;
    static $freezeDate;

    public static function freeze()
    {
        self::$freezeDate = self::now();
        self::$freeze = true;
    }

    public static function nowAsString()
    {
        return self::$freeze ? self::$freezeDate->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
    }

    public static function now()
    {
        return self::$freeze ? self::$freezeDate : new DateTime();
    }
}