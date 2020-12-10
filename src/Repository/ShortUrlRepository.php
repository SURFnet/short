<?php


namespace App\Repository;


use App\Entity\ShortUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
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

    public function save(ShortUrl $shortUrl): void
    {
        $this->_em->persist($shortUrl);
    }

    public function findLatest(int $page, int $itemsPerPage, string $filter = null, UserInterface $user = null): PaginationInterface
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.created', 'DESC');

        if ($filter) {
            $qb
                ->where('o.longUrl LIKE :pattern')
                ->orWhere('o.shortUrl = :filter')
                ->orWhere('o.owner = :filter')
                ->setParameter('pattern', '%'.$filter.'%')
                ->setParameter('filter', $filter)
            ;
        }

        if ($user) {
            $qb->andWhere('o.owner = :owner')
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
