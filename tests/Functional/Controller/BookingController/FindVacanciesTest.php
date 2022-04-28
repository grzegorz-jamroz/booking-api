<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller\BookingController;

use App\Domain\Booking\Status;
use App\Tests\Functional\FunctionalTestCase;

class FindVacanciesTest extends FunctionalTestCase
{
    const TABLE = 'booking';
    const URI_VACANCIES = '/booking/vacancies';

    public function testShouldReturnEmptyArray()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);

        // When
        $this->client->request('GET', self::URI_VACANCIES);

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
        $this->client->request('GET', self::URI_VACANCIES);

        // Then
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $response);
        $this->assertEquals($expected, $response[0]);
    }

    public function testShouldReturnArrayWithTwoVacancies()
    {
        // Expect & Given
        $this->truncateTable(self::TABLE);
        $expected = [
            [
                'id' => 1,
                'date' => strtotime("10-04-2022"),
                'status' => Status::VACANCY->value,
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
        $this->client->request('POST', self::URI_VACANCIES, [], [], [], json_encode($vacancyPeriod));

        // When
        $this->client->request('GET', self::URI_VACANCIES);

        // Then
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $response);
        $this->assertEquals($expected, $response);
    }
}
