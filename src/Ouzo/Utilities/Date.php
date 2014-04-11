<?php
namespace Ouzo\Utilities;

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
        $date = Clock::now();
        $date->add(new DateInterval($interval));
        return $date->format($format);
    }

    public static function modifyNow($interval, $format = self::DEFAULT_TIME_FORMAT)
    {
        return Clock::now()->modify($interval)->format($format);
    }

    public static function modify($dateAsString, $interval, $format = self::DEFAULT_TIME_FORMAT)
    {
        $dateTime = new DateTime($dateAsString);
        return $dateTime->modify($interval)->format($format);
    }

    public static function beginningOfDay($date)
    {
        return self::formatDate($date) . ' 00:00:00';
    }

    public static function endOfDay($date)
    {
        return self::formatDate($date) . ' 23:59:59.9999';
    }

    public static function formatTime($date, $format = 'H:i')
    {
        return self::formatDate($date, $format);
    }
}