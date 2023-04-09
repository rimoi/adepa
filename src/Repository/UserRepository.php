<?php

namespace App\Repository;

use App\Constant\UserConstant;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

     /**
     * @return User[] Returns an array of User objects
     */
    public function findByRole(string $role, bool $enabled = false): array
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.roles like :role')
            ->setParameter('role', '%'.$role.'%');

        $qb->andWhere('u.enabled = :enabled')
            ->setParameter('enabled', $enabled);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return User[]
     */
    public function findByCategory(array $parentIds): array
    {
        $qb = $this->createQueryBuilder('u')
            ->join('u.categories', 'c')
            ->join('c.parent', 'cp')
            ->where('cp.id IN (:parents)')
            ->setParameter('parents', $parentIds)
            ->andWhere('u.isVerified = :enabled')
            ->andWhere('u.enabled = :enabled')
            ->setParameter('enabled', true);

        return $qb->getQuery()->getResult();
    }


//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



    public function getFreelances(): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.id')
            ->where('u.enabled = :enabled')
            ->setParameter('enabled', true)
            ->andWhere('u.roles LIKE :freelance')
            ->setParameter('freelance', '%'.UserConstant::ROLE_FREELANCE.'%');

        return $qb->getQuery()->getResult();
    }
}
