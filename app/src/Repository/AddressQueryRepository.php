<?php

namespace App\Repository;

use App\Entity\AddressQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AddressQuery>
 *
 * @method AddressQuery|null find($id, $lockMode = null, $lockVersion = null)
 * @method AddressQuery|null findOneBy(array $criteria, array $orderBy = null)
 * @method AddressQuery[]    findAll()
 * @method AddressQuery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressQueryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AddressQuery::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getAddressQuery(string $asset, string $address, \DateTime $before, \DateTime $after, int $threshold)
    {
        return $this
            ->createQueryBuilder('aq')
            ->select('aq')
            ->where('aq.asset = :asset')
            ->andWhere('aq.address = :address')
            ->andWhere('aq.before = :before')
            ->andWhere('aq.after = :after')
            ->andWhere('aq.threshold = :threshold')
            ->setParameter('asset', $asset)
            ->setParameter('address', $address)
            ->setParameter('before', $before)
            ->setParameter('after', $after)
            ->setParameter('threshold', $threshold)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return AddressQuery[] Returns an array of AddressQuery objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AddressQuery
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
