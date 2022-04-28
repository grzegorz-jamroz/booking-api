<?php

declare(strict_types=1);

namespace App\Query\Booking;

use App\Domain\Collection\DayCollection;
use Doctrine\DBAL\Connection;
use PlainDataTransformer\Transform;

class FindByPeriodQuery extends FindQuery
{
    public function __construct(
        Connection $connection,
        private int $dateFrom,
        private int $dateTo,
    ) {
        parent::__construct($connection);
    }

    protected function prepareQuery(): void
    {
        parent::prepareQuery();
        $days = new DayCollection($this->dateFrom, $this->dateTo);
        $days = array_map(fn (mixed $value) => Transform::toString($value), $days->toArray());
        $this->where(
            $this->expr()->in('b.date', $days),
        );
    }
}
