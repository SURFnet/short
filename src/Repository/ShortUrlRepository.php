<?php


namespace App\Repository;


use App\Entity\ShortUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method ShortUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortUrl[]    findAll()
 * @method ShortUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ShortUrlRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, ShortUrl::class);

        $this->paginator = $paginator;
    }

    public function findLatest(int $page, int $itemsPerPage, UserInterface $user = null): PaginationInterface
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.created', 'DESC');

        if ($user) {
            $qb->where('o.owner = :owner')
                ->setParameter('owner', $user->getUsername())
            ;
        }

        return $this->paginator->paginate(
            $qb,
            $page,
            $itemsPerPage
        );
    }
}
