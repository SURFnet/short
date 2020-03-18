<?php
namespace App\Controller;

use App\Entity\ShortUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedirectController extends AbstractController
{
    public function redirectAction(string $req) : Response
    {
        $repository = $this->getDoctrine()->getRepository(ShortUrl::class);
        $shortUrl = $repository->findOneBy(['shortUrl' => $req]);

        if ($shortUrl === null) {
            throw new NotFoundHttpException("The requested URL was not found.");
        }
        if ($shortUrl->getDeleted()) {
            throw new GoneHttpException("The requested URL is no longer available.");
        }

        $shortUrl->addClick();
        $em = $this->getDoctrine()->getManager();
        $em->persist($shortUrl);
        $em->flush();

        return $this->redirect($shortUrl->getLongUrl(), 307);
    }
}
