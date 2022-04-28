<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller\BookingController;

use App\Tests\Functional\FunctionalTestCase;

class CreateReservationTest extends FunctionalTestCase
{
    const TABLE = 'booking';
    const URI = '/booking';
    const URI_VACANCIES = '/booking/vacancies';
    const URI_RESERVATIONS = '/booking/reservations';

    public function testShouldCreateReservationForOneDay()
    {
        // Expect
        $this->truncateTable(self::TABLE);
        $vacancyPeriod = [
            'from' => strtotime("10-04-2022"),
            'to' => strtotime("10-05-2022"),
        ];
        $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($vacancyPeriod));
        $this->client->request('GET', self::URI_VACANCIES);
        $this->assertCount(31, json_decode($this->client->getResponse()->getContent(), true));

        // Given
        $reservationPeriod = [
            'from' => strtotime("15-04-2022"),
            'to' => strtotime("15-04-2022"),
        ];

        // When & Then
        $this->client->request('POST', self::URI_RESERVATIONS, [], [], [], json_encode($reservationPeriod));
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals('Reservation has been created.', $response['message']);

        $this->client->request('GET', self::URI_VACANCIES);
        $this->assertCount(30, json_decode($this->client->getResponse()->getContent(), true));
        $this->client->request('GET', self::URI_RESERVATIONS);
        $this->assertCount(1, json_decode($this->client->getResponse()->getContent(), true));
    }

    public function testShouldCreateReservationWhenGivenPeriodIsIncludedInVacancyPeriod()
    {
        // Expect
        $this->truncateTable(self::TABLE);
        $vacancyPeriod = [
            'from' => strtotime("10-04-2022"),
            'to' => strtotime("10-05-2022"),
        ];
        $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($vacancyPeriod));
        $this->client->request('GET', self::URI_VACANCIES);
        $this->assertCount(31, json_decode($this->client->getResponse()->getContent(), true));

        // Given
        $reservationPeriod = [
            'from' => strtotime("15-04-2022"),
            'to' => strtotime("25-04-2022"),
        ];

        // When & Then
        $this->client->request('POST', self::URI_RESERVATIONS, [], [], [], json_encode($reservationPeriod));
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals('Reservation has been created.', $response['message']);

        $this->client->request('GET', self::URI_VACANCIES);
        $this->assertCount(20, json_decode($this->client->getResponse()->getContent(), true));
        $this->client->request('GET', self::URI_RESERVATIONS);
        $this->assertCount(11, json_decode($this->client->getResponse()->getContent(), true));
    }

    public function testShouldReturnErrorWhenPeriodIsMissing()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);
        $reservations = [
            [],
            [
                'from' => null,
                'to' => null,
            ],
            [
                'from' => strtotime("10-03-2022"),
                'to' => null,
            ],
        ];
        $this->client->request('GET', self::URI);
        $this->assertEquals([], json_decode($this->client->getResponse()->getContent(), true));

        // When & Then
        foreach ($reservations as $reservation) {
            $this->client->request('POST', self::URI_RESERVATIONS, [], [], [], json_encode($reservation));
            $response = json_decode($this->client->getResponse()->getContent(), true);
            $this->assertResponseStatusCodeSame(400);
            $this->assertStringContainsString('Missing parameter ', $response['message']);
        }
    }

    public function testShouldReturnErrorWhenGivenPeriodIsInvalid()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);
        $bookingPeriods = [
            [
                'from' => 0,
                'to' => 0,
            ],
            [
                'from' => '',
                'to' => '',
            ],
            [
                'from' => false,
                'to' => false,
            ],
        ];
        $this->client->request('GET', self::URI);
        $this->assertEquals([], json_decode($this->client->getResponse()->getContent(), true));

        // When & Then
        foreach ($bookingPeriods as $bookingPeriod) {
            $this->client->request('POST', self::URI_RESERVATIONS, [], [], [], json_encode($bookingPeriod));
            $response = json_decode($this->client->getResponse()->getContent(), true);
            $this->assertResponseStatusCodeSame(400);
            $this->assertEquals('Unable to create reservation. Given period is invalid.', $response['message']);
        }
    }

    public function testShouldReturnErrorWhenGivenPeriodIsNotAvailable()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);
        $bookingData = [
            'from' => strtotime("10-04-2022"),
            'to' => strtotime("10-05-2022"),
        ];
        $reservations = [
            [
                'from' => strtotime("10-03-2022"),
                'to' => strtotime("20-03-2022"),
            ],
            [
                'from' => strtotime("10-03-2022"),
                'to' => strtotime("10-04-2022"),
            ],
            [
                'from' => strtotime("10-06-2022"),
                'to' => strtotime("20-06-2022"),
            ],
            [
                'from' => strtotime("10-05-2022"),
                'to' => strtotime("20-06-2022"),
            ],
            [
                'from' => strtotime("01-04-2022"),
                'to' => strtotime("20-05-2022"),
            ],
        ];
        $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($bookingData));
        $this->client->request('GET', self::URI_VACANCIES);
        $this->assertCount(31, json_decode($this->client->getResponse()->getContent(), true));

        // When & Then
        foreach ($reservations as $reservation) {
            $this->client->request('POST', self::URI_RESERVATIONS, [], [], [], json_encode($reservation));
            $response = json_decode($this->client->getResponse()->getContent(), true);
            $this->assertResponseStatusCodeSame(400);
            $this->assertEquals('Unable to create reservation. Given period is not available.', $response['message']);
        }
    }

    public function testShouldReturnErrorWhenGivenPeriodIsIncludedInTwoVacancyPeriodsButThereIsGapBetweenThem()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);
        $vacancies = [
            [
                'from' => strtotime("10-04-2022"),
                'to' => strtotime("20-04-2022"),
            ],
            [
                'from' => strtotime("01-05-2022"),
                'to' => strtotime("20-05-2022"),
            ],
        ];
        $reservation = [
            'from' => strtotime("15-04-2022"),
            'to' => strtotime("10-05-2022"),
        ];

        foreach ($vacancies as $vacancy) {
            $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($vacancy));
        }

        $this->client->request('GET', self::URI_VACANCIES);
        $this->assertCount(31, json_decode($this->client->getResponse()->getContent(), true));

        // When
        $this->client->request('POST', self::URI_RESERVATIONS, [], [], [], json_encode($reservation));
        $response = json_decode($this->client->getResponse()->getContent(), true);

        //Then
        $this->assertResponseStatusCodeSame(400);
        $this->assertEquals('Unable to create reservation. Given period is not available.', $response['message']);
    }
}
