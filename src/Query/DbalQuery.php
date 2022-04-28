<?php

declare(strict_types=1);

namespace App\Query;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;

abstract class DbalQuery extends QueryBuilder
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
        $this->prepareQuery();
    }

    public function executeQuery(): Result
    {
        return $this->getConnection()->executeQuery(
            $this->getSQL(),
            $this->getParameters(),
            $this->getParameterTypes(),
            $this->getQueryCacheProfile()
        );
    }

    abstract protected function prepareQuery(): void;

    public function getQueryCacheProfile(): ?QueryCacheProfile
    {
        return null;
    }
}
