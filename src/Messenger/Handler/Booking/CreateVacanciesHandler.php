<?php

declare(strict_types=1);

namespace App\Messenger\Handler\Booking;

use App\Domain\Booking\Status;
use App\Domain\Collection\DayCollection;
use App\Entity\Booking;
use App\Messenger\Command\Booking\CreateVacancies;
use App\Repository\BookingRepository;
use PlainDataTransformer\Transform;

class CreateVacanciesHandler
{
    public function __construct(
        private BookingRepository $repository
    ) {
    }

    public function __invoke(CreateVacancies $command): void
    {
        $from = $command->getDateFrom();
        $to = $command->getDateTo();

        if ($from === 0 || $to === 0) {
            throw new \Exception('Unable to create Vacancies. Given period is invalid.');
        }

        $days = new DayCollection($from, $to);
        $bookingDates = $this->repository->findDataByPeriodQuery($from, $to);

        foreach ($bookingDates as $bookingDate) {
            $days->remove(Transform::toInt($bookingDate['date']));
        }

        foreach ($days as $date) {
            $booking = new Booking(
                0,
                Transform::toInt($date),
                Status::VACANCY
            );
            $this->repository->add($booking);
        }
    }
}
