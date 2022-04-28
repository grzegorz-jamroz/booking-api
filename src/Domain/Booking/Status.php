<?php

declare(strict_types=1);

namespace App\Domain\Booking;

enum Status: int
{
    case VACANCY = 1;
    case RESERVED = 2;
}
