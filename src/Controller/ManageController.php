<?php
namespace App\Controller;

use App\Entity\ShortUrl;
use App\Exception\ShortUserException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class ManageController extends AbstractController
{
    private function generateShortcode() : string
    {
        $shortcode_length = $this->getParameter('app.shortcode.length');
        $shortcode_chars = $this->getParameter('app.shortcode.chars');

        $code = "";

        for($i=0; $i < $shortcode_length; ++$i) {
            $code .= substr($shortcode_chars, mt_rand(0,strlen($shortcode_chars)-1), 1);
        }
        return $code;
    }

    private function verifyCSRF(string $name, Request $request) : void
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid($name, $submittedToken)) {
            throw new \Exception("Invalid CSRF token received for $name");
        }
    }

    /**
     * @Route("/manage/create")
     */
    public function createAction(Request $request) : Response
    {
        $this->verifyCSRF('create-form', $request);

        $repository = $this->getDoctrine()->getRepository(ShortUrl::class);
        $em = $this->getDoctrine()->getManager();

        $me = $this->getUser()->getUsername();

        $long_url = $request->request->get('url');

        if($long_url === null) {
            throw new \Exception("Missing URL");
        }

        $long_url = $this->validateUrl($long_url);

        $shortUrl = new ShortUrl();
        $shortUrl->setOwner($me);
        $shortUrl->setLongUrl($long_url);

        if($this->isGranted('ROLE_ADMIN') && $shortcode = $request->request->get('shortcode')) {
            $shortcode = strtolower(trim($shortcode));
            if ( preg_match($this->getParameter('app.shortcode.forbiddenchars'), $shortcode) ) {
                throw new ShortUserException("Shortcode contains invalid characters");
            }

            $urlexists = $repository->findOneBy(['shortUrl' => $shortcode]);
            if ($urlexists !== null) {
                throw new ShortUserException("Shortcode already exists");
            }

            $shortUrl->setShortUrl($shortcode);
            $em->persist($shortUrl);
            $em->flush();
        } else {
            $maxtries = $this->getParameter('app.shortcode.maxtries');

            $success = false;
            for($i = 0; $i < $maxtries; ++$i) {
                $shortcode = $this->generateShortcode();

                $urlexists = $repository->findOneBy(['shortUrl' => $shortcode]);

                if ( $urlexists === null ) {
                    // There is a tiny race condition here
                    $shortUrl->setShortUrl($shortcode);
                    $em->persist($shortUrl);
                    $em->flush();
                    $success = true;
                    break;
                }
            }

            if (!$success) { // too many tries
                throw new \Exception("Error while selecting shortcode");
            }
        }

        return $this->render('manage/create.html.twig', ['short_url' => $shortUrl]);
    }

    /**
     * @Route("/manage/edit")
     */
    public function editAction(Request $request) : Response
    {
        $this->verifyCSRF('edit-form', $request);

        $repository = $this->getDoctrine()->getRepository(ShortUrl::class);

        $me = $this->getUser()->getUsername();

        $url = $request->request->get('url');
        if($url === null) {
            throw new \Exception("Missing URL");
        }

        $shortUrl = $repository->findOneBy(['shortUrl' => $url]);

        if($shortUrl === null) {
            throw new \Exception("URL to edit not found");
        }
        if($shortUrl->getOwner() !== $me && !$this->isGranted('ROLE_ADMIN')) {
            throw new \Exception("URL is not owned by logged in user and user is not admin");
        }
        if($shortUrl->getDeleted()) {
            throw new \Exception("Cannot edit deleted URL");
        }

        $to_delete = $request->request->get('delete');
        $long_url = $request->request->get('new_long_url');

        $updated = false;
        if($to_delete !== null) {
            $shortUrl->setDeleted(true);
            $updated = true;
        } elseif($long_url !== null) {
            $long_url = $this->validateUrl($long_url);
            $shortUrl->setLongUrl($long_url);
            $updated = true;
        }

        if($updated) {
            $shortUrl->setUpdated();

            $em = $this->getDoctrine()->getManager();
            $em->persist($shortUrl);
            $em->flush();

            // not currently presented
            //$this->addFlash('notice', 'The URL was updated.');

            return $this->redirectToRoute('app_manage_index');
        }

        return $this->render('manage/edit.html.twig', ['short_url' => $shortUrl]);
    }
}