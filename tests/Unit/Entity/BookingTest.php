<?php
declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Domain\Booking\Status;
use App\Entity\Booking;
use App\Tests\Functional\FunctionalTestCase;

class BookingTest extends FunctionalTestCase
{
    public function testShouldReturnDesiredJsonSerializeResponse()
    {
        // Given
        $expected = [
            'id' => 1,
            'date' => 1649548800,
            'status' => Status::RESERVED->value,
        ];
        $booking = new Booking(1, strtotime('10-04-2022'), Status::RESERVED);

        // When & Then
        $this->assertEquals($expected, $booking->jsonSerialize());
    }
}
