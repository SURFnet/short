<?php
namespace App\Controller;

use App\Entity\ShortUrl;
use Endroid\QrCode\QrCode;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedirectController extends AbstractController
{
    private function lookup(string $req) : ShortUrl
    {
        $repository = $this->getDoctrine()->getRepository(ShortUrl::class);
        $shortUrl = $repository->findOneBy(['shortUrl' => $req]);

        if ($shortUrl === null) {
            throw new NotFoundHttpException("The requested URL was not found.");
        }
        if ($shortUrl->getDeleted()) {
            throw new GoneHttpException("The requested URL is no longer available.");
        }

        return $shortUrl;
    }

    public function redirectAction(string $req) : Response
    {
        $shortUrl = $this->lookup($req);

        $shortUrl->addClick();
        $em = $this->getDoctrine()->getManager();
        $em->persist($shortUrl);
        $em->flush();

        return $this->redirect($shortUrl->getLongUrl(), 307);
    }

    public function previewAction(string $req) : Response
    {
        $shortUrl = $this->lookup(rtrim($req,'+'));

        return $this->render('redirect/preview.html.twig', [
            'short' => $shortUrl->getShortUrl(),
            'long' => $shortUrl->getLongUrl(),
            'created' => $shortUrl->getCreated(),
            'updated' => $shortUrl->getUpdated(),
            ] );
    }

    public function quickResponseAction(string $req, Request $request) : Response
    {
        $shortUrl = $this->lookup(rtrim($req,'~'));

        $url = $this->getParameter('app.urldomain') . '/' . $shortUrl->getShortUrl();
        $fullurl = 'https://' . $url;

        $qrCode = new QrCode($fullurl);
        $qrCode->setValidateResult(false);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setLabel($url);
        $image = $qrCode->writeString();

        $response = new Response();
        $response->headers->set('Content-Type', $qrCode->getContentType());
        $response->headers->set('Content-Length', strlen($image));
        $response->setEtag(md5($image));
        $response->setPublic();
        $response->isNotModified($request);
        $response->setContent($image);

        return $response;
    }

}
