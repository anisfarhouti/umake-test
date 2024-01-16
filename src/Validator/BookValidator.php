<?php

namespace App\Validator;

use App\Util\DateUtil;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Contracts\Translation\TranslatorInterface;

class BookValidator
{
    const FOODTRUCK_ID = 'foodtruck_id';
    const RESERVATION_DATE = 'reservation_date';

    /**
     * @param InputBag $data
     * @return array
     */
    public static function validate(InputBag $data, TranslatorInterface $translator): array
    {
        $error = [];
        $foodTruckId = $data?->get(self::FOODTRUCK_ID);
        $reservationDate = DateUtil::createFromFormat($data?->get(self::RESERVATION_DATE));

        if (empty($foodTruckId)) {
            $error[self::FOODTRUCK_ID] = $translator->trans('field.mandatory') . ' :' . self::FOODTRUCK_ID;
        }

        if (!$reservationDate) {
            $error[self::RESERVATION_DATE] = $translator->trans('field.date.format');
        }

        return $error;
    }
}