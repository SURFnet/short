<?php
namespace App\Controller;

use App\Entity\ShortUrl;
use Endroid\QrCode\QrCode;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
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

    public function redirectAction(Request $request, string $req) : Response
    {
        $req = rtrim($req, ").!:,;i \n\r\t\v\x00");
        $shortUrl = $this->lookup($req);

        $method = $request->getMethod();
        if ($method === $request::METHOD_GET || $method === $request::METHOD_POST) {
            $shortUrl->addClick();
            $em = $this->getDoctrine()->getManager();
            $em->persist($shortUrl);
            $em->flush();
        }

        return $this->redirect($shortUrl->getLongUrl(), 307);
    }

    public function previewAction(string $req) : Response
    {
        $shortUrl = $this->lookup(rtrim($req,'+'));

        return $this->render('redirect/preview.html.twig', [
            'short' => $shortUrl->getShortUrl(),
            'long' => $shortUrl->getLongUrl(),
            ] );
    }

    public function quickResponseAction(string $req, Request $request) : Response
    {
        $shortUrl = $this->lookup(rtrim($req,'~'));

        $url = $this->getParameter('app.urldomain') . '/' . $shortUrl->getShortUrl();
        $protocol = $this->getParameter('app.protocol');
        $fullurl = $protocol . '://' . $url;

        $qrCode = new QrCode($fullurl);
        $qrCode->setValidateResult(false);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setLabel($url);

        $format = $request->query->get('format');
        if($format === 'pdf') {
            $qrCode->setWriterByName('fpdf');
            $qrCode->setLabelFontSize(72);
        } elseif(in_array($format, ['png','eps','svg'])) {
            $qrCode->setWriterByName($format);
        }

        $image = $qrCode->writeString();

        $response = new Response();
        $response->headers->set('Content-Type', $qrCode->getContentType());
        $response->headers->set('Content-Length', strlen($image));
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_INLINE,
            'qr_' . $shortUrl->getShortUrl() . '.' . $format
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->setEtag(md5($image));
        $response->setPublic();
        $response->isNotModified($request);
        $response->setContent($image);

        return $response;
    }

}
