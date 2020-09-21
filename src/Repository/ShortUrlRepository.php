<?php


namespace App\Repository;


use App\Entity\ShortUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function findLatest(int $page, int $itemsPerPage, UserInterface $user = null): Paginator
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.created', 'DESC');

        if ($user) {
            $qb->where('o.owner = :owner')
                ->setParameter('owner', $user->getUsername())
            ;
        }

        $query = $qb->getQuery();

        return $this->createPaginator($query, $page, $itemsPerPage);
    }

    private function createPaginator(Query $query, int $page, int $limit): Paginator
    {
        $paginator = new Paginator($query);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
        ;

        return $paginator;
    }
}
