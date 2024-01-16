<?php

namespace App\Controller;

use App\Entity\FoodTruck;
use App\Service\BookingService;
use App\Trait\BookDateTrait;
use App\Util\DateUtil;
use App\Validator\BookValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class BookController extends AbstractController
{
    use BookDateTrait;

    #[Route('/book', name: 'book', methods: ['POST'])]
    public function book(
        EntityManagerInterface $manager,
        Request $request,
        BookingService $bookingService,
        TranslatorInterface $translator
    )
    {
        $data = $request->getPayload();
        $errors = BookValidator::validate($data, $translator);
        if (!empty($errors)) {
            return $this->json(["error" => $errors]);
        }

        $foodTruckId = $data?->get('foodtruck_id');
        //create date in a specific format, and return false, if not valid date is given
        $reservationDate = DateUtil::createFromFormat($data?->get('reservation_date'));

        // if given date match past or today's date then throw error
        if ($this->isPastOrTodayDate($reservationDate)) {
            return $this->json(
                [
                    "error" => $translator->trans("date.past.or.today")],
                Response::HTTP_BAD_REQUEST
            );
        }

        /** @var FoodTruck $foodtruck */
       $foodtruck = $manager->getRepository(FoodTruck::class)->find($foodTruckId);

        $reservationFailure = $this->checkReservationConditionsFailure(
            $bookingService,
            $foodtruck,
            $foodTruckId,
            $reservationDate,
            $translator
        );

        if ($reservationFailure !== null) {
            return $reservationFailure;
        }

        try {
            $reservation = $bookingService->bookLocation($foodtruck, $reservationDate);
        } catch (\Exception $exception) {
            // if a logger is implemented, we may log the exception
            return $this->json(["error" => $exception->getMessage()]);
        }

        return $this->json(
            [
                "success" => $translator->trans(
                    'reservation.success',
                    [
                        'location' => $reservation?->getLocation()->getName(),
                        'dateReservation' => $reservationDate->format("d/m/Y")
                    ]
                )
            ],
            Response::HTTP_CREATED
        );
    }

    private function checkReservationConditionsFailure(
        BookingService $bookingService,
        ?FoodTruck $foodtruck,
        string $foodTruckId,
        \DateTime|bool $reservationDate,
        TranslatorInterface $translator
    ): ?JsonResponse
    {
        if (empty($foodtruck)) {
            return $this->json(
                [
                    "error" => $translator->trans('foodtruck.not.found', ['foodTruckId' => $foodTruckId])
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        //check if a location is available for the given date
        $isLocationAvailable = $bookingService->isLocationAvailable($reservationDate);
        if (!$isLocationAvailable) {
            return $this->json(["error" => $translator->trans("location.unavailable")], Response::HTTP_CONFLICT);
        }

        //check if foodtruck have a reservation in the same week
        if ($bookingService->haveSameWeekReservation($foodTruckId, $reservationDate)) {
            return $this->json(["error" => $translator->trans('week.reservation.restriction')], Response::HTTP_CONFLICT);
        }

        return null;
    }
}