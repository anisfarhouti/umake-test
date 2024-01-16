<?php

namespace App\Util;

use DateTime;
use DateTimeZone;

class DateUtil
{
    /**
     * @throws \Exception
     */
    public static function getDateNow(): DateTime
    {
        return (new DateTime("now", new DateTimeZone('Europe/Paris')));
    }

    public static function format($date, $format = "d/m/Y"): string
    {
        return $date->format($format);
    }

    public static function createFromFormat($date, $format = "d/m/Y"): DateTime|bool
    {
        if (!$date) {
            return false;
        }
        return DateTime::createFromFormat($format, $date, new DateTimeZone('Europe/Paris'));
    }

    public static function getDay(DateTime $reservationDate): ?string
    {
        return $reservationDate?->format('l');
    }

    public static function getWeekYearNumber(DateTime $date, $format = "W-Y")
    {
        $strtime = strtotime($date->format("d-m-Y"));
        return date($format, $strtime);
    }
}