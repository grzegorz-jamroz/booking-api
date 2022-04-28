<?php

namespace App\Controller;

use App\Domain\Booking\Status;
use App\Messenger\Command\Booking\CreateReservation;
use App\Messenger\Command\Booking\CreateVacancies;
use App\Repository\BookingRepository;
use PlainDataTransformer\Transform;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends MainController
{
    #[Route('/booking', name: 'booking_find', methods: ['GET'])]
    public function find(BookingRepository $repository): JsonResponse
    {
        return new JsonResponse($repository->findData());
    }

    #[Route('/booking/vacancies', name: 'booking_vacancies_find', methods: ['GET'])]
    public function findVacancies(BookingRepository $repository): JsonResponse
    {
        return new JsonResponse($repository->findDataByStatus(Status::VACANCY));
    }

    #[Route('/booking/vacancies', name: 'booking_vacancies_create', methods: ['POST'])]
    public function createVacancies(): JsonResponse
    {
        $this->handle(new CreateVacancies(
            Transform::toInt($this->getApiRequestRequiredField('from')),
            Transform::toInt($this->getApiRequestRequiredField('to'))
        ));

        return $this->json([
            'message' => 'Vacancies have been created.',
        ]);
    }

    #[Route('/booking/reservations', name: 'booking_reservations_find', methods: ['GET'])]
    public function findReservations(BookingRepository $repository): JsonResponse
    {
        return new JsonResponse($repository->findDataByStatus(Status::RESERVED));
    }

    #[Route('/booking/reservations', name: 'booking_reservations_create', methods: ['POST'])]
    public function createReservations(): JsonResponse
    {
        $this->handle(new CreateReservation(
            Transform::toInt($this->getApiRequestRequiredField('from')),
            Transform::toInt($this->getApiRequestRequiredField('to'))
        ));

        return $this->json([
            'message' => 'Reservation has been created.',
        ]);
    }
}
