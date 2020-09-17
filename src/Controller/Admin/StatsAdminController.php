<?php
namespace App\Controller\Admin;

use App\Entity\ShortUrl;
use App\Exception\ShortUserException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manage/admin/stats", name="app_manage_stats")
 * @IsGranted("ROLE_ADMIN")
 */
final class StatsAdminController extends AbstractController
{
    public function __invoke(Request $request) : Response
    {
        $repo = $this->getDoctrine()->getRepository(ShortUrl::class);

        $q_total = $repo->createQueryBuilder('s')
            ->select('count(s.id) as cnt, sum(s.clicks) as clk');
        $q_unique = $repo->createQueryBuilder('s')
            ->select('count(distinct s.owner) as uniqueusers');

        $total = $q_total->getQuery()->getResult();
        $unique = $q_unique->getQuery()->getResult();
        $stats = ['total' => $total[0] + $unique[0]];

        $van = $request->query->get('van');
        $tot = $request->query->get('tot');

        if ($van !== null && $tot !== null) {
            $q_total->where("s.created >= :van")->setParameter('van', $van)->andWhere("s.created <= :tot")->setParameter('tot', $tot);
            $total = $q_total->getQuery()->getResult();
            $q_unique->where("s.created >= :van")->setParameter('van', $van)->andWhere("s.created <= :tot")->setParameter('tot', $tot);
            $unique = $q_unique->getQuery()->getResult();
            $stats['period'] = $total[0] + $unique[0];
        }

        return $this->render('admin/stats.html.twig',
            ['stats' => $stats, 'van' => $van, 'tot' => $tot]);
    }
}
