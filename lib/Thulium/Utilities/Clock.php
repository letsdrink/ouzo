<?php
namespace Thulium\Utilities;

class Clock
{
    static $freeze = false;
    static $freezeDate;

    public static function freeze()
    {
        self::$freezeDate = self::now();
        self::$freeze = true;
    }

    public static function now()
    {
        return self::$freeze ? self::$freezeDate : date('Y-m-d H:i:s');
    }
}