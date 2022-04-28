<?php

declare(strict_types=1);

namespace App\Messenger\Handler\Booking;

use App\Domain\Booking\Status;
use App\Domain\Collection\DayCollection;
use App\Messenger\Command\Booking\CreateReservation;
use App\Repository\BookingRepository;
use PlainDataTransformer\Transform;

class CreateReservationHandler
{
    public function __construct(
        private BookingRepository $repository
    ) {
    }

    public function __invoke(CreateReservation $command): void
    {
        $from = $command->getDateFrom();
        $to = $command->getDateTo();

        if ($from === 0 || $to === 0) {
            throw new \Exception('Unable to create reservation. Given period is invalid.');
        }

        $vacancies = $this->repository->findDataByStatusAndPeriod(Status::VACANCY, $from, $to);
        $days = new DayCollection($from, $to);

        if ($days->count() !== count($vacancies)) {
            throw new \Exception('Unable to create reservation. Given period is not available.');
        }

        $vacancyIds = array_map(fn (array $vacancy) => Transform::toInt($vacancy['id']), $vacancies);
        $this->repository->updateStatus(Status::RESERVED, $vacancyIds);
    }
}
