<?php
namespace Ouzo\Utilities;

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
        return self::now()->format('Y-m-d H:i:s');
    }

    public static function now()
    {
        $date = new DateTime();
        if (self::$freeze) {
            $date->setTimestamp(self::$freezeDate->getTimestamp());
        }
        return $date;
    }
}