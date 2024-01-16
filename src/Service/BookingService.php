<?php

namespace App\Service;

use App\Entity\FoodTruck;
use App\Entity\Location;
use App\Entity\Reservation;
use App\Util\DateUtil;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class BookingService
{
    public function __construct(private readonly EntityManagerInterface $manager) {}

    /**
     * @param DateTime $reservationDate
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function isLocationAvailable(DateTime $reservationDate): bool
    {
        $qb = $this->manager->createQueryBuilder();
        $qb
            ->select('COUNT (r.id)')
            ->from(Reservation::class, 'r')
            ->andWhere("r.date = :date");

        $qb->setParameter(':date', $reservationDate->format('Y-m-d'));

        $countLocation = $qb->getQuery()->getSingleScalarResult();
        $day = DateUtil::getDay($reservationDate);

        if (($day == "Friday" && $countLocation >= 6) || ($countLocation >= 7)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $foodTruckId
     * @param DateTime|bool $reservationDate
     * @return bool
     */
    public function haveSameWeekReservation(string $foodTruckId, DateTime|bool $reservationDate): bool
    {
        $weekYearNumber = DateUtil::getWeekYearNumber($reservationDate);
        $reservation = $this->manager->getRepository(Reservation::class)->findOneBy([
            'foodTruck' => $foodTruckId,
            'weekNumber' => $weekYearNumber
        ]);

        return !empty($reservation);
    }

    /**
     * @param FoodTruck $foodTruck
     * @param DateTime|bool $reservationDate
     * @return Reservation
     */
    public function bookLocation(FoodTruck $foodTruck, DateTime|bool $reservationDate): Reservation
    {
        $weekYearNumber = DateUtil::getWeekYearNumber($reservationDate);

        //get location available for that date
        //if a location does not exists in table reservation for a given date, then it's available
        $qb = $this->manager->createQueryBuilder();
        $qb->select('l')
            ->from(Location::class, 'l')
            ->leftJoin('l.reservations', 'reservation', 'WITH', 'reservation.date = :date')
            ->andWhere($qb->expr()->isNull('reservation.id'))
            ->setParameter('date', $reservationDate->format('Y-m-d'));

        $availableLocation = $qb->getQuery()?->getResult()[0];

        $reservation = (new Reservation())
            ->setFoodTruck($foodTruck)
            ->setLocation($availableLocation)
            ->setDate($reservationDate)
            ->setWeekNumber($weekYearNumber)
        ;

        $this->manager->persist($reservation);
        $this->manager->flush();

        return $reservation;
    }
}