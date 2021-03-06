<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
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

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

     /**
      * @return User[] Returns an array of User objects
      */
    public function findByRole($role)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles = :val')
            ->setParameter('val', $role)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(100000)
            ->getQuery()
            ->getResult()
        ;
    }
    

      /**
      * @return User[] Returns an array of User objects
      */
    public function getEtudiants($role)
    {
        $qb = $this->_em->createQueryBuilder();
            $qb = '
            SELECT *
            FROM user u
            WHERE u.roles = :role
            ';
            return $qb->getQuery()->getResult();


    }
    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
    
    /**
     * @return  User[] Returns an array of User objects
     */

    public function findByRoles($value): ?array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->andwhere('u.roles LIKE :role')
            ->setParameter('role', '%"'.$value.'"%')
            ->getQuery()
            ->getResult();
    }
}
