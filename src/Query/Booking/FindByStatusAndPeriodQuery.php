<?php

declare(strict_types=1);

namespace App\Query\Booking;

use App\Domain\Booking\Status;
use Doctrine\DBAL\Connection;

class FindByStatusAndPeriodQuery extends FindByPeriodQuery
{
    public function __construct(
        Connection $connection,
        private Status $status,
        int $dateFrom,
        int $dateTo,
    ) {
        parent::__construct($connection, $dateFrom, $dateTo);
    }

    protected function prepareQuery(): void
    {
        parent::prepareQuery();
        $this->andWhere('status = :status');
        $this->setParameter('status', $this->status->value);
    }
}
