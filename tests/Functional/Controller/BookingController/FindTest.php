<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller\BookingController;

use App\Domain\Booking\Status;
use App\Tests\Functional\FunctionalTestCase;

class FindTest extends FunctionalTestCase
{
    const TABLE = 'booking';
    const URI = '/booking';
    const URI_VACANCIES = '/booking/vacancies';
    const URI_RESERVATIONS = '/booking/reservations';

    public function testShouldReturnEmptyArray()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);

        // When
        $this->client->request('GET', self::URI);

        // Then
        $this->assertResponseIsSuccessful();
        $this->assertEquals([], json_decode($this->client->getResponse()->getContent(), true));
    }

    public function testShouldReturnArrayWithOneVacancy()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);
        $expected = [
            'id' => 1,
            'date' => strtotime("10-04-2022"),
            'status' => Status::VACANCY->value,
        ];
        $vacancyPeriod = [
            'from' => strtotime("10-04-2022"),
            'to' => strtotime("10-04-2022"),
        ];
        $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($vacancyPeriod));

        // When
        $this->client->request('GET', self::URI);

        // Then
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $response);
        $this->assertEquals($expected, $response[0]);
    }

    public function testShouldReturnArrayWithOneReservation()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);
        $expected = [
            'id' => 1,
            'date' => strtotime("10-04-2022"),
            'status' => Status::RESERVED->value,
        ];
        $vacancyPeriod = [
            'from' => strtotime("10-04-2022"),
            'to' => strtotime("10-04-2022"),
        ];
        $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($vacancyPeriod));
        $this->client->request('POST', self::URI_RESERVATIONS, [], [], [], json_encode($vacancyPeriod));

        // When
        $this->client->request('GET', self::URI);

        // Then
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $response);
        $this->assertEquals($expected, $response[0]);
    }

    public function testShouldReturnArrayWithOneVacancyAndOneReservation()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);
        $expected = [
            [
                'id' => 1,
                'date' => strtotime("10-04-2022"),
                'status' => Status::RESERVED->value,
            ],
            [
                'id' => 2,
                'date' => strtotime("11-04-2022"),
                'status' => Status::VACANCY->value,
            ]
        ];
        $vacancyPeriod = [
            'from' => strtotime("10-04-2022"),
            'to' => strtotime("11-04-2022"),
        ];
        $reservationPeriod = [
            'from' => strtotime("10-04-2022"),
            'to' => strtotime("10-04-2022"),
        ];
        $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($vacancyPeriod));
        $this->client->request('POST', self::URI_RESERVATIONS, [], [], [], json_encode($reservationPeriod));

        // When
        $this->client->request('GET', self::URI);

        // Then
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $response);
        $this->assertEquals($expected, $response);
    }
}
