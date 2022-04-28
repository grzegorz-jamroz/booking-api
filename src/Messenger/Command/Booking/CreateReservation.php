<?php

declare(strict_types=1);

namespace App\Messenger\Command\Booking;

class CreateReservation
{
    public function __construct(
        private int $dateFrom,
        private int $dateTo
    ) {
    }

    public function getDateFrom(): int
    {
        return $this->dateFrom;
    }

    public function getDateTo(): int
    {
        return $this->dateTo;
    }
}
