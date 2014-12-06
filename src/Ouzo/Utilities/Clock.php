<?php
namespace Ouzo\Utilities;

use DateTime;

/**
 * Class Clock
 * @package Ouzo\Utilities
 */
class Clock
{
    public static $freeze = false;
    public static $freezeDate;

    /**
     * Freeze clock on given date, if given null or none freeze on now.
     *
     * @param null $date
     */
    public static function freeze($date = null)
    {
        self::$freeze = false;
        self::$freezeDate = $date ? new DateTime($date) : self::now();
        self::$freeze = true;
    }

    /**
     * Return format date which is currently freeze.
     *
     * Example:
     * <code>
     * Clock::freeze('2011-01-02 12:34');
     * $result = Clock::nowAsString('Y-m-d');
     * </code>
     * Result:
     * <code>
     * 2011-01-02
     * </code>
     *
     * @param string $format
     * @return string
     */
    public static function nowAsString($format = 'Y-m-d H:i:s')
    {
        return self::now()->format($format);
    }

    /**
     * Return DateTime object which is currently freeze.
     *
     * @return DateTime
     */
    public static function now()
    {
        $date = new DateTime();
        if (self::$freeze) {
            $date->setTimestamp(self::$freezeDate->getTimestamp());
        }
        return $date;
    }
}
