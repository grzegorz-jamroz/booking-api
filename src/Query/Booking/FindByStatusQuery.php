<?php

declare(strict_types=1);

namespace App\Query\Booking;

use App\Domain\Booking\Status;
use Doctrine\DBAL\Connection;

class FindByStatusQuery extends FindQuery
{
    public function __construct(
        Connection $connection,
        private Status $status,
    ) {
        parent::__construct($connection);
    }

    protected function prepareQuery(): void
    {
        parent::prepareQuery();
        $this->andWhere('status = :status');
        $this->setParameter('status', $this->status->value);
    }
}
