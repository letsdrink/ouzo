<?php
namespace Ouzo\Utilities;

use DateTime;
use Ouzo\I18n;

class TimeAgo
{
    private $_date;

    public function __construct($date)
    {
        $this->_date = $date;
    }

    public function asString()
    {
        $date = new DateTime($this->_date);
        if ($this->_showJustNow()) {
            return I18n::t('timeAgo.justNow');
        }
        if ($minutesAgo = $this->_showMinutesAgo()) {
            return I18n::t('timeAgo.minAgo', array('label' => $minutesAgo));
        }
        if ($this->_showTodayAt()) {
            return I18n::t('timeAgo.todayAt', array('label' => $date->format('H:i')));
        }
        if ($this->_showYesterdayAt()) {
            return I18n::t('timeAgo.yesterdayAt', array('label' => $date->format('H:i')));
        }
        if ($this->_showThisYear()) {
            $month = I18n::t('timeAgo.month.' . $date->format('n'));
            return I18n::t('timeAgo.thisYear', array('day' => $date->format('j'), 'month' => $month));
        }

        return $date->format('Y-m-d');
    }

    private function _showJustNow()
    {
        return $this->_getDateDiff() <= 60;
    }

    private function _showMinutesAgo()
    {
        $difference = $this->_getDateDiff();
        return ($difference > 60 && $difference < 3600) ? floor($difference / 60) : null;
    }

    private function _showTodayAt()
    {
        $difference = $this->_getDateDiff();
        return $this->_isSameDay() && $difference >= 3600 && $difference < 86400;
    }

    private function _getDateDiff()
    {
        return $this->_nowAsTimestamp() - $this->_dateAsTimestamp();
    }

    private function _nowAsTimestamp()
    {
        return Clock::now()->getTimestamp();
    }

    private function _dateAsTimestamp()
    {
        return strtotime($this->_date);
    }

    private function _isSameDay()
    {
        $now = $this->_nowAsTimestamp();
        $date = $this->_dateAsTimestamp();
        return date('Y-m-d', $now) == date('Y-m-d', $date);
    }

    private function _showYesterdayAt()
    {
        $now = $this->_nowAsTimestamp();
        $date = $this->_dateAsTimestamp();
        if (date('Y-m', $now) == date('Y-m', $date)) {
            return date('d', $now) - date('d', $date) == 1;
        }
        return false;
    }

    private function _showThisYear()
    {
        $now = $this->_nowAsTimestamp();
        $date = $this->_dateAsTimestamp();
        return date('Y', $now) == date('Y', $date);
    }

    public static function create($date)
    {
        return new self($date);
    }
}