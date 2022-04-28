<?php

declare(strict_types=1);

namespace App\Query\Booking;

use App\Query\DbalQuery;
use Doctrine\DBAL\Connection;

class FindQuery extends DbalQuery
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    protected function prepareQuery(): void
    {
        $this->select('*');
        $this->from('booking', 'b');
    }
}
