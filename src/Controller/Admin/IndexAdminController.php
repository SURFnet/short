<?php


namespace App\Controller\Admin;


use App\Entity\ShortUrl;
use App\Form\ShortUrlType;
use App\Repository\ShortUrlRepository;
use App\Services\GenerateUniqueShortUrl;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/manage/admin/", defaults={"page": "1"}, name="app_manage_admin", methods={"GET", "POST"})
 * @Route("/manage/admin/page/{page<[1-9]\d*>}", name="app_manage_admin_paginated", methods={"GET"})
 */
final class IndexAdminController extends AbstractController
{
    /**
     * @var ShortUrlRepository
     */
    private $repository;
    /**
     * @var GenerateUniqueShortUrl
     */
    private $generateUniqueShortUrl;

    public function __construct(ShortUrlRepository $repository, GenerateUniqueShortUrl $generateUniqueShortUrl)
    {
        $this->repository = $repository;
        $this->generateUniqueShortUrl = $generateUniqueShortUrl;
    }

    public function __invoke(Request $request, int $page): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $shortUrl = new ShortUrl();
        $shortUrl->setOwner($user->getUsername());

        $form = $this->createForm(ShortUrlType::class, $shortUrl, [
            'is_admin' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shortUrl = $this->generateUniqueShortUrl->generate($shortUrl->getLongUrl(), $shortUrl->getOwner(), $shortUrl->getShortUrl());

            return $this->redirectToRoute('app_manage_show', ['shortUrl' => $shortUrl->getShortUrl()]);
        }

        $itemsPerPage = $this->getParameter('app.shortlink.pagination');
        $shortUrls = $this->repository->findLatest($page, $itemsPerPage);

        $itemsPerPage = $shortUrls->getQuery()->getMaxResults();
        $numPages = ceil($shortUrls->count() / $itemsPerPage);

        return $this->render('admin/index.html.twig', [
            'form' => $form->createView(),
            'num_pages' => $numPages,
            'page' => $page,
            'route' => 'app_manage_admin_paginated',
            'short_urls' => $shortUrls
        ]);
    }
}
