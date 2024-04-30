<?php

namespace App\Repository;

use App\Constant\ReservationType;
use App\Constant\UserConstant;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function save(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getTerminateReservated(): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.educatheure', 'e')
            ->join('r.owner', 'o')
            ->join('r.affected', 'u')
            ->andWhere('r.endAt < :now')
            ->andWhere('o.roles NOT LIKE :role')
            ->andWhere('r.status = :accepted')
            ->setParameter('accepted', ReservationType::ACCEPTED)
            ->setParameter('now', new \DateTime('now', new \DateTimeZone('Europe/Paris')))
            ->setParameter('role', '%'.UserConstant::ROLE_ADMIN.'%')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getAffectedReservations(User $user): array
    {
        $qb = $this->createQueryBuilder('r');

        return $qb->join('r.educatheure', 'e')
            ->leftjoin('e.user', 'u')
            ->leftJoin('r.users', 'users')
            ->where('u.id = :user_id OR users IN (:user_id)')
            ->setParameter('user_id', $user->getId())
            ->andWhere('r.status = :status')
            ->setParameter('status', ReservationType::CREATED)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }



//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
