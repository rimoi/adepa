<?php

namespace App\Repository;

use App\Entity\Educatheure;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Educatheure>
 *
 * @method Educatheure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Educatheure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Educatheure[]    findAll()
 * @method Educatheure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EducatheureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Educatheure::class);
    }

    public function save(Educatheure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Educatheure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getEducatheureByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('e');

        $qb->join('e.users', 'u')
            ->where('u.id = :currentUser')
            ->andWhere('u.archived = :archived')
            ->setParameter('currentUser', $user->getId())
            ->setParameter('archived', false)
            ->orderBy('e.id', 'DESC')
            ;

        return $qb->getQuery()->getResult();
    }

    public function search(array $args): array
    {
        $qb = $this->createQueryBuilder('e');

        $qb->andWhere('e.archived = :archived')
            ->andWhere('e.published = :published')
            ->setParameter('archived', false)
            ->setParameter('published', true);

        if ($args['zipCode'] ?? false) {
            $qb->andWhere('e.zipCode = :zipCode')
                ->setParameter('zipCode', $args['zipCode']);
        }
        if ($args['search'] ?? false) {
            $qb->andWhere('e.title LIKE :search')
                ->setParameter('search', '%'.$args['search'].'%');
        }
        if ($args['category'] ?? false) {
            $qb->join('e.categories', 'c')
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $args['category']);
        }
        if (
            ($args['publics'] ?? false)
            && array_filter($args['publics'])
        ) {
            $qb
                ->andWhere('e.publicType IN (:publics)')
                ->setParameter('publics', $args['publics']);
        }

        if ($args['priceMin'] ?? false) {
            $qb->andWhere('e.price >= :priceMin')
                ->setParameter('priceMin', (int) $args['priceMin']);
        }
        if ($args['priceMax'] ?? false) {
            $qb->andWhere('e.price <= :priceMax')
                ->setParameter('priceMax', (int) $args['priceMax']);
        }
        if ($args['participant'] ?? false) {
            $qb->andWhere('e.numberParticipant >= :participant')
                ->setParameter('participant', (int) $args['participant']);
        }
        if ($args['intervention'] ?? false) {
            $qb->andWhere('e.nombreIntervention >= :intervention')
                ->setParameter('intervention', (int) $args['intervention']);
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Educatheure[] Returns an array of Educatheure objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Educatheure
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
