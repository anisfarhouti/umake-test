<?php

namespace App\Repository;

use App\Entity\FoodTruck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FoodTruck>
 *
 * @method FoodTruck|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoodTruck|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoodTruck[]    findAll()
 * @method FoodTruck[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodTruckRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodTruck::class);
    }
}
