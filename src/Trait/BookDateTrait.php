<?php

namespace App\Trait;

use App\Util\DateUtil;
use DateTime;
use DateTimeZone;

trait BookDateTrait
{
    /**
     * @param $reservationDate
     * @return bool
     * @throws \Exception
     */
    public function isPastOrTodayDate($reservationDate): bool
    {
        $reservationDate = $reservationDate->format('Y-m-d');

        $stampToday = DateUtil::getDateNow()->getTimestamp();
        $today = date('Y-m-d', $stampToday);

        return strtotime($reservationDate) <= strtotime($today);
    }
}