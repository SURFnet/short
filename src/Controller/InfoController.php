<?php
namespace App\Controller;

use App\Entity\ShortUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function indexAction() : Response
    {
        return $this->render('info/index.html.twig');
    }

    /**
     * @Route("/about")
     */
    public function aboutAction() : Response
    {
        return $this->render('info/about.html.twig');
    }

    /**
     * @Route("/support")
     */
    public function supportAction() : Response
    {
        return $this->render('info/support.html.twig');
    }

    /**
     * @Route("/privacy")
     */
    public function privacyAction() : Response
    {
        return $this->render('info/privacy.html.twig');
    }

    /**
     * @Route("/health")
     */
    public function healthAction() : Response
    {
        $repo = $this->getDoctrine()->getRepository(ShortUrl::class);

        $total = $repo->createQueryBuilder('s')
            ->select('count(s.id)')
            ->getQuery()
            ->getSingleScalarResult();

        if($total > $this->getParameter('app.health.minimumurls')) {
            return $this->json(['status' => 'OK'], 200);
        }

        return $this->json(['status' => 'ERROR'], 500);
    }
}
