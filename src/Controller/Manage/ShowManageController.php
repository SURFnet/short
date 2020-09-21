<?php


namespace App\Controller\Manage;


use App\Entity\ShortUrl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/manage/{shortUrl}/show", name="app_manage_show", methods={"GET"})
 * @IsGranted("SHOW", subject="instance")
 */
final class ShowManageController extends AbstractController
{
    public function __invoke(ShortUrl $instance): Response
    {
        if ($instance->getDeleted()) {
            throw $this->createNotFoundException();
        }

        return $this->render('manage/show.html.twig', [
            'short_url' => $instance
        ]);
    }
}
