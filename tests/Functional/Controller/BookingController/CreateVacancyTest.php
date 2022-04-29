<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller\BookingController;

use App\Tests\Functional\FunctionalTestCase;

class CreateVacancyTest extends FunctionalTestCase
{
    const TABLE = 'booking';
    const URI = '/booking';
    const URI_VACANCIES = '/booking/vacancies';
    const URI_RESERVATIONS = '/booking/reservations';

    public function testShouldCreateVacancyPeriod()
    {
        // Expect
        $this->truncateTable(self::TABLE);
        $this->client->request('GET', self::URI);
        $this->assertEquals([], json_decode($this->client->getResponse()->getContent(), true));

        // Given
        $vacancy = [
            'from' => strtotime("10-04-2022"),
            'to' => strtotime("10-05-2022"),
        ];

        // When
        $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($vacancy));
        $response = json_decode($this->client->getResponse()->getContent(), true);

        // Then
        $this->assertResponseIsSuccessful();
        $this->assertEquals('Vacancies have been created.', $response['message']);
    }

    public function testShouldCreateVacancyPeriodAndSkipAlreadyBookedDays()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);
        $vacancyPeriod = [
            'from' => strtotime("10-04-2022"),
            'to' => strtotime("10-05-2022"),
        ];
        $reservationPeriod = [
            'from' => strtotime("15-04-2022"),
            'to' => strtotime("25-04-2022"),
        ];
        $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($reservationPeriod));
        $this->client->request('POST', self::URI_RESERVATIONS, [], [], [], json_encode($reservationPeriod));

        $this->client->request('GET', self::URI_RESERVATIONS);
        $this->assertCount(11, json_decode($this->client->getResponse()->getContent(), true));

        $this->client->request('GET', self::URI_VACANCIES);
        $this->assertCount(0, json_decode($this->client->getResponse()->getContent(), true));

        // When & Then
        $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($vacancyPeriod));
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals('Vacancies have been created.', $response['message']);

        $this->client->request('GET', self::URI_VACANCIES);
        $this->assertCount(20, json_decode($this->client->getResponse()->getContent(), true));

        $this->client->request('GET', self::URI_RESERVATIONS);
        $this->assertCount(11, json_decode($this->client->getResponse()->getContent(), true));
    }

    public function testShouldReturnErrorWhenPeriodIsMissing()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);
        $bookingPeriods = [
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
        foreach ($bookingPeriods as $bookingPeriod) {
            $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($bookingPeriod));
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
            $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($bookingPeriod));
            $response = json_decode($this->client->getResponse()->getContent(), true);
            $this->assertResponseStatusCodeSame(400);
            $this->assertEquals('Unable to create Vacancies. Given period is invalid.', $response['message']);
        }
    }

    public function testShouldReturnErrorWhenDateFromIsGreaterThanDateTo()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);
        $vacancyPeriod = [
            'from' => strtotime("10-05-2022"),
            'to' => strtotime("10-04-2022"),
        ];

        // When
        $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($vacancyPeriod));
        $response = json_decode($this->client->getResponse()->getContent(), true);

        // Then
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('Unable to create Vacancies. Given period is invalid.', $response['message']);
    }
}
