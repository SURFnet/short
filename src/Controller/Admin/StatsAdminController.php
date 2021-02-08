<?php
namespace App\Controller\Admin;

use App\Entity\ShortUrl;
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

        $from = $request->query->get('from');
        $to = $request->query->get('to');

        if ($from !== null && $to !== null) {
            $q_total->where("s.created >= :from")->setParameter('from', $from)->andWhere("s.created <= :to")->setParameter('to', $to);
            $total = $q_total->getQuery()->getResult();
            $q_unique->where("s.created >= :from")->setParameter('from', $from)->andWhere("s.created <= :to")->setParameter('to', $to);
            $unique = $q_unique->getQuery()->getResult();
            $stats['period'] = $total[0] + $unique[0];
        }

        if ($_SERVER['APP_NEW_UI']) {
            return $this->render('new-ui/admin/stats.html.twig',
                ['stats' => $stats, 'from' => $from, 'to' => $to]);
        }

        return $this->render('admin/stats.html.twig',
            ['stats' => $stats, 'from' => $from, 'to' => $to]);
    }
}
