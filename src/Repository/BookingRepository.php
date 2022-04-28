<?php

namespace App\Repository;

use App\Domain\Booking\Status;
use App\Entity\Booking;
use App\Query\Booking\FindByPeriodQuery;
use App\Query\Booking\FindByStatusAndPeriodQuery;
use App\Query\Booking\FindByStatusQuery;
use App\Query\Booking\FindQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use PlainDataTransformer\Transform;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Booking $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findData(): array
    {
        return (new FindQuery($this->_em->getConnection()))->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findDataByStatus(Status $status): array
    {
        $query = (new FindByStatusQuery($this->_em->getConnection(), $status));

        return $query->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findDataByPeriodQuery(
        int $dateFrom,
        int $dateTo
    ): array {
        $query = new FindByPeriodQuery(
            $this->_em->getConnection(),
            $dateFrom,
            $dateTo,
        );

        return $query->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findDataByStatusAndPeriod(
        Status $status,
        int $dateFrom,
        int $dateTo
    ): array {
        $query = new FindByStatusAndPeriodQuery(
            $this->_em->getConnection(),
            $status,
            $dateFrom,
            $dateTo,
        );

        return $query->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param int[] $ids
     */
    public function updateStatus(Status $status, array $ids): void
    {
        $conn = $this->_em->getConnection();
        $conn->beginTransaction();

        try {
            foreach ($ids as $id) {
                $conn->update(
                    'booking',
                    ['status' => $status->value],
                    ['id' => Transform::toInt($id)]
                );
            }

            $conn->commit();
        } catch (\Exception) {
            $conn->rollBack();
            throw new \Exception('Unable to update status.');
        }
    }
}
