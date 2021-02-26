<?php

namespace App\Repository;

use App\Entity\Institution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Institution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Institution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Institution[]    findAll()
 * @method Institution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstitutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Institution::class);
    }
}
