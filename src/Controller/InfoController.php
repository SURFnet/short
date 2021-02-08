<?php
namespace App\Controller;

use App\Entity\ShortUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class InfoController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function indexAction() : Response
    {
        if ($this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_manage_index');
        }

        if ($_SERVER['APP_NEW_UI']) {
            return $this->render('new-ui/info/index.html.twig');
        }

        return $this->render('info/index.html.twig');
    }

    /**
     * @Route("/about")
     */
    public function aboutAction() : Response
    {
        if ($_SERVER['APP_NEW_UI']) {
            return $this->render('new-ui/info/about.html.twig');
        }

        return $this->render('info/about.html.twig');
    }

    /**
     * @Route("/support")
     */
    public function supportAction() : Response
    {
        if ($_SERVER['APP_NEW_UI']) {
            return $this->render('new-ui/info/support.html.twig');
        }

        return $this->render('info/support.html.twig');
    }

    /**
     * @Route("/privacy")
     */
    public function privacyAction() : Response
    {
        if ($_SERVER['APP_NEW_UI']) {
            return $this->render('new-ui/info/privacy.html.twig');
        }

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
