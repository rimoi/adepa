<?php

namespace App\Repository;

use App\Entity\Mission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mission>
 *
 * @method Mission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mission[]    findAll()
 * @method Mission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    public function save(Mission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Mission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Mission[] Returns an array of Mission objects
     */
    public function getMissionByCriteria(User $user, array $categories = []): array
    {
        /**
         * @var Mission[] $missions
         */
        $missions = $this->createQueryBuilder('m')
            ->join('m.categories', 'c')
            ->where('c.id IN (:categories)')
            ->andWhere('m.archived = :archived AND m.published = :published ')
            ->setParameter('published', true)
            ->setParameter('archived', false)
            ->setParameter('categories', $categories)
            ->orderBy('m.started', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $this->checkIsAffected($missions, $user);
    }


    private function getMissionNotAffected(): array
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->join('m.user', 'u')
            ->andWhere('m.archived = :archived AND m.published = :published ')
            ->setParameter('published', true)
            ->setParameter('archived', false)
            ->orderBy('m.started');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Mission[] Returns an array of Mission objects
     */
    public function getMissions(User $user): array
    {
        /**
         * @var Mission[] $missions
         */
        $missions = $this->getMissionNotAffected();

        return $this->checkIsAffected($missions, $user);
    }

    private function checkIsAffected($missions, User $user): array
    {
        $resultats = [];

        foreach ($missions as $mission) {

            $include = true;

            if ($mission->getExclusives()->toArray() && $mission->getUser()->getId() !== $user->getId()) {
                $include = false;

                foreach ($mission->getExclusives() as $exclusive) {
                    if ($exclusive->getUser()->getId() === $user->getId()) {
                        $include = true;
                    }
                }
            }


            if ($include) {
                $resultats[$mission->getId()] = $mission;
            }
        }

        return $resultats;
    }



//    public function findOneBySomeField($value): ?Mission
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
