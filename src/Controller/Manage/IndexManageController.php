<?php


namespace App\Controller\Manage;

use App\Entity\ShortUrl;
use App\Form\ShortUrlType;
use App\Repository\ShortUrlRepository;
use App\Services\GenerateUniqueShortUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/manage/", name="app_manage_index", methods={"GET", "POST"})
 */
final class IndexManageController extends AbstractController
{
    /**
     * @var ShortUrlRepository
     */
    private $repository;
    /**
     * @var GenerateUniqueShortUrl
     */
    private $generateShortUrlCode;

    public function __construct(ShortUrlRepository $repository, GenerateUniqueShortUrl $generateShortUrlCode)
    {
        $this->repository = $repository;
        $this->generateShortUrlCode = $generateShortUrlCode;
    }

    public function __invoke(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $shortUrl = new ShortUrl();
        $shortUrl->setOwner($user->getUsername());

        $form = $this->createForm(ShortUrlType::class, $shortUrl);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shortUrl = $this->generateShortUrlCode->generate($shortUrl->getLongUrl(), $shortUrl->getOwner());

            return $this->redirectToRoute('app_manage_show', ['shortUrl' => $shortUrl->getShortUrl()]);
        }

        $shortUrls = $this->repository->findByOwner($user);

        return $this->render(
            'manage/index.html.twig',
            [
                'short_urls' => $shortUrls,
                'form' => $form->createView()
            ]
        );
    }
}