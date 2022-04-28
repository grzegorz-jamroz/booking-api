<?php

declare(strict_types=1);

namespace App\Domain\Collection;

use Doctrine\Common\Collections\ArrayCollection;

class DayCollection extends ArrayCollection
{
    public function __construct(
        int $dateFrom,
        int $dateTo
    ) {
        parent::__construct([]);
        $this->init($dateFrom, $dateTo);
    }

    private function init(
        int $dateFrom,
        int $dateTo
    ): void {
        $from = (new \DateTimeImmutable())->setTimestamp($dateFrom);
        $to = (new \DateTimeImmutable())->setTimestamp($dateTo + 86400);
        $dates = new \DatePeriod($from, \DateInterval::createFromDateString('1 day'), $to);

        foreach ($dates as $date) {
            $this->set($date->getTimestamp(), $date->getTimestamp());
        }
    }
}
