<?php
namespace Thulium\Utilities;

use DateInterval;
use DateTime;

class Date
{
    const DEFAULT_TIME_FORMAT = 'Y-m-d H:i';

    public static function formatDate($date, $format = 'Y-m-d')
    {
        if (!$date) {
            return null;
        }
        $date = new DateTime($date);
        return $date->format($format);
    }

    public static function formatDateTime($date, $format = self::DEFAULT_TIME_FORMAT)
    {
        return self::formatDate($date, $format);
    }

    public static function addInterval($interval, $format = self::DEFAULT_TIME_FORMAT)
    {
        $date = new DateTime();
        $date->add(new DateInterval($interval));
        return $date->format($format);
    }
}