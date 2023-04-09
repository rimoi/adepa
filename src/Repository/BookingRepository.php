<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
use App\helper\ArrayHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function save(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Booking[] Returns an array of Booking objects
     */
    public function getTerminateBooking(): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.mission', 'm')
            ->where('b.archived = :not_archived')
            ->andWhere('m.ended <= :now')
            ->andWhere('b.validate = :not_archived')
            ->setParameter('now', new \DateTime('now', new \DateTimeZone('Europe/Paris')))
            ->setParameter('not_archived', false)
            ->andWhere('b.ConfirmStarted IS NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getUserMatch(User $user): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select('u.id')
            ->join('b.mission', 'm')
            ->join('b.user', 'u')
            ->join('m.user', 'user')
            ->where('user.id = :user_id')
            ->andWhere('u.enabled = :enabled')
            ->setParameter('enabled', true)
            ->setParameter('user_id', $user->getId());

        $res = $qb->getQuery()->getResult();

        return ArrayHelper::columnize($res, '[id]');
    }

//    public function findOneBySomeField($value): ?Booking
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
