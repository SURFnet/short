<?php


namespace App\Repository;


use App\Entity\ShortUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method ShortUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortUrl[]    findAll()
 * @method ShortUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ShortUrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortUrl::class);
    }
}
