<?php


namespace App\Core\Util;

use Carbon\Carbon;
use DateTime;

class DateUtil
{

    public static $STANDARD_DATE_FORMAT = "Y-m-d";
    public static $_STANDARD_DATE_FORMAT = "d/m/Y";
    public static $STANDARD_TIME_FORMAT = "H:i:s";
    public static $EUROPE_DATE_FORMAT = "d-m-Y";
    public static $YEAR_SERIAL_FORMAT = "Y";
    public static $MONTH_SERIAL_FORMAT = "Ym";
    public static $DAY_SERIAL_FORMAT = "Ymd";
    public static $HL7_DATETIME_STAMP_FORMAT = "YmdHi";

    private static function setTimezome()
    {
        date_default_timezone_set("Africa/Dar_es_Salaam");
    }

    public static function now()
    {
        self::setTimezome();
        $DateTime = new Carbon();
        return $DateTime->format("Y-m-d H:i:s");
    }

    public static function time_now()
    {
        self::setTimezome();
        $DateTime = new Carbon();
        return $DateTime->format("H:i");
    }

    public static function today()
    {
        self::setTimezome();
        $DateTime = new Carbon(self::now());
        return $DateTime->format("Y-m-d");
    }

    public static function yesterday()
    {
        self::setTimezome();
        $DateTime = new Carbon(self::offset(self::today(), '-1 day'));
        return $DateTime->format("Y-m-d");
    }

    public static function inDay($numberOfDays)
    {
        self::setTimezome();
        $DateTime = new Carbon(self::offset(self::today(), $numberOfDays . ' day'));
        return $DateTime->format("Y-m-d");
    }

    public static function ofDay($numberOfDays)
    {
        self::setTimezome();
        $DateTime = new Carbon(self::offset(self::today(), '-' . $numberOfDays . ' day'));
        return $DateTime->format("Y-m-d");
    }

    public static function thisYear()
    {
        self::setTimezome();
        $DateTime = new Carbon(self::now());
        return $DateTime->format("Y");
    }

    public static function offset($Date, $dateOffset)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        $DateTime->modify($dateOffset);
        return $DateTime->format("Y-m-d H:i:s");
    }

    public static function time_offset($Date, $dateOffset)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        $DateTime->modify($dateOffset);
        return $DateTime->format("H:i");
    }

    public static function day($Date)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        return $DateTime->format("D");
    }

    public static function minuteDiff($date1, $date2)
    {
        self::setTimezome();
        $datetime1 = strtotime($date1);
        $datetime2 = strtotime($date2);
        $interval  = abs($datetime2 - $datetime1);
        $minutes   = round($interval / 60);
        return intval($minutes);
    }

    public static function _minuteDiff($date1, $date2)
    {
        self::setTimezome();
        $datetime1 = strtotime($date1);
        $datetime2 = strtotime($date2);
        $interval  = $datetime2 - $datetime1;
        $minutes   = round($interval / 60);
        return intval($minutes);
    }

    public static function hourDiff($date1, $date2)
    {
        $minuteDiff = self::minuteDiff($date1, $date2);
        $hours = round($minuteDiff / 60);
        return intval($hours);
    }

    public static function dayDiff($date1, $date2)
    {
        $minuteDiff = self::minuteDiff($date1, $date2);
        $days = round($minuteDiff / (60 * 24));
        return intval($days);
    }

    public static function _dayDiff($date1, $date2)
    {
        $minuteDiff = self::_minuteDiff($date1, $date2);
        $days = round($minuteDiff / (60 * 24));
        return intval($days);
    }

    public static function standardFormat($Date)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        return $DateTime->format("Y-m-d");
    }

    public static function format($date, $format)
    {
        self::setTimezome();
        $dateTime = new Carbon($date);
        return $dateTime->format($format);
    }

    public static function modified($Date, $modification)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        $DateTime->modify($modification);
        return $DateTime->format("Y-m-d");
    }

    public static function toWord($Date)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        return $DateTime->format("F d, Y");
    }

    public static function strToDate($date, $format)
    {
        self::setTimezome();
        $dateTime = DateTime::createFromFormat($format, $date)->format("Y-m-d");
        return $dateTime;
    }

    public static function thisMonthStartDate($format = null)
    {
        self::setTimezome();
        $DateTime = new Carbon('first day of this month');
        if (empty($format)) {
            return $DateTime->format(self::$STANDARD_DATE_FORMAT);
        } else {
            return $DateTime->format($format);
        }
    }

    public static function lastMonthStartDate($format = null)
    {
        self::setTimezome();
        $DateTime = new Carbon('first day of last month');
        if (empty($format)) {
            return $DateTime->format(self::$STANDARD_DATE_FORMAT);
        } else {
            return $DateTime->format($format);
        }
    }

    public static function lastMonthEndDate($format = null)
    {
        self::setTimezome();
        $DateTime = new Carbon('last day of last month');
        if (empty($format)) {
            return $DateTime->format(self::$STANDARD_DATE_FORMAT);
        } else {
            return $DateTime->format($format);
        }
    }

    public static function thisMonthEndDate()
    {
        self::setTimezome();
        $DateTime = new Carbon('last day of this month');
        return $DateTime->format("Y-m-d");
    }

    public static function thisYearStartDate()
    {
        self::setTimezome();
        $DateTime = new Carbon('first day of january');
        return $DateTime->format("Y-m-d");
    }

    public static function thisYearEndDate()
    {
        self::setTimezome();
        $DateTime = new Carbon('last day of December');
        return $DateTime->format("Y-m-d");
    }

    public static function lastYearStartDate()
    {
        self::setTimezome();
        $DateTime = new Carbon('first day of january last year');
        return $DateTime->format("Y-m-d");
    }

    public static function lastYearEndDate()
    {
        self::setTimezome();
        $DateTime = new Carbon('last day of December last year');
        return $DateTime->format("Y-m-d");
    }

    public static function dayBeginning($Date)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        $DateTime->setTime(0, 0, 0);
        return $DateTime->format("Y-m-d H:i:s");
    }

    public static function minuteBeginning($Date)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        $DateTime->setTime($DateTime->format('H'), $DateTime->format('i'), 0);
        return $DateTime->format("Y-m-d H:i:s");
    }

    public static function monthBeginning($Date)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        $DateTime->modify('first day of this month');
        return $DateTime->format("Y-m-d");
    }

    public static function monthEnding($Date)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        $DateTime->modify('last day of this month');
        return $DateTime->format("Y-m-d");
    }

    public static function dayEnding($Date)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        $DateTime->setTime(23, 59, 59);
        return $DateTime->format("Y-m-d H:i:s");
    }

    public static function minuteEnding($Date)
    {
        self::setTimezome();
        $DateTime = new Carbon($Date);
        $DateTime->setTime($DateTime->format('H'), $DateTime->format('i'), 59);
        return $DateTime->format("Y-m-d H:i:s");
    }

    public static function age($given_date)
    {
        self::setTimezome();
        $birth_date     = new Carbon($given_date);
        $current_date   = new Carbon();

        $diff           = $birth_date->diff($current_date);

        return $years     = $diff->y . " year(s) " . $diff->m . " month(s) " . $diff->d . " day(s)";
    }

    public static function ageAsPer($given_date, $transaction_date)
    {
        self::setTimezome();
        $birth_date     = new Carbon($given_date);
        $current_date   = new Carbon($transaction_date);

        $diff           = $birth_date->diff($current_date);

        return $years     = $diff->y . " year(s) " . $diff->m . " month(s) " . $diff->d . " day(s)";
    }

    public static function ageInYear($given_date)
    {
        self::setTimezome();
        $birth_date     = new Carbon($given_date);
        $current_date   = new Carbon();

        $diff           = $birth_date->diff($current_date);

        return $years     = $diff->y;
    }

    public static function validateDateFormat($dateStr, $format)
    {
        self::setTimezome();
        $date = DateTime::createFromFormat($format, $dateStr);
        return $date && ($date->format($format) === $dateStr);
    }

    public static function getDateBetween($first, $last, $step = '+1 day', $output_format = 'Y-m-d')
    {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while ($current <= $last) {
            $dateInStr = date($output_format, $current);
            $dates[] = array("Given_Date" => $dateInStr);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    public static function isBewteen($date, $startDate, $endDate)
    {
        if (($date >= $startDate) && ($date <= $endDate)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isFutureDate($date)
    {
        $today = DateUtil::today();
        if ($date > $today) {
            return true;
        } else {
            return false;
        }
    }

    public static function isNotFutureDate($date)
    {
        return !self::isFutureDate($date);
    }

    public static function isFutureTime($date)
    {
        $today = DateUtil::now();
        if ($date > $today) {
            return true;
        } else {
            return false;
        }
    }

    public static function isNotFutureTime($date)
    {
        return !self::isFutureTime($date);
    }

    public static function isNotBewteen($date, $startDate, $endDate)
    {
        return !self::isBewteen($date, $startDate, $endDate);
    }

    public static function finishStartDate($_startDate)
    {
        if (self::validateDateFormat($_startDate, 'Y-m-d')) {
            $fromDate = DateUtil::dayBeginning($_startDate);
        } else {
            $fromDate = DateUtil::minuteBeginning($_startDate);
        }
        return $fromDate;
    }

    public static function finishEndDate($_endDate)
    {
        if (self::validateDateFormat($_endDate, 'Y-m-d')) {
            $toDate = DateUtil::dayEnding($_endDate);
        } else {
            $toDate = DateUtil::minuteEnding($_endDate);
        }
        return $toDate;
    }

    public static function dailyKey()
    {
        self::setTimezome();
        $DateTime = new Carbon(self::now());
        return $DateTime->format(self::$DAY_SERIAL_FORMAT);
    }

    public static function monthlyKey()
    {
        self::setTimezome();
        $DateTime = new Carbon(self::now());
        return $DateTime->format(self::$MONTH_SERIAL_FORMAT);
    }

    public static function yearlyKey()
    {
        self::setTimezome();
        $DateTime = new Carbon(self::now());
        return $DateTime->format(self::$YEAR_SERIAL_FORMAT);
    }
}
