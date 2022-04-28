<?php

namespace App\Entity;

use App\Domain\Booking\Status;
use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer', length: 10)]
    private int $date;

    #[ORM\Column(type: 'smallint')]
    private int $status;

    public function __construct(
        int $id,
        int $date,
        Status $status,
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->status = $status->value;
    }

    /**
     * @return array<string, int>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'status' => $this->status,
        ];
    }
}
