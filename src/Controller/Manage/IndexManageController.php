<?php


namespace App\Controller\Manage;

use App\Entity\ShortUrl;
use App\Form\ShortUrlType;
use App\Repository\ShortUrlRepository;
use App\Services\GenerateUniqueShortUrl;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/manage/", defaults={"page": "1"}, name="app_manage_index", methods={"GET", "POST"})
 * @Route("/manage/page/{page<[1-9]\d*>}", name="app_manage_index_paginated", methods={"GET", "POST"})
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

    public function __invoke(Request $request, int $page): Response
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

        $pagination = $this->getPaginationWithSearchFilter($request, $page);

        return $this->render(
            'manage/index.html.twig',
            [
                'form' => $form->createView(),
                'pagination' => $pagination,
                'route' => 'app_manage_index_paginated',
            ]
        );
    }

    /**
     * @param Request $request
     * @param int $page
     * @return PaginationInterface
     */
    protected function getPaginationWithSearchFilter(Request $request, int $page): PaginationInterface
    {
        $filterValue = $request->query->get('filterValue');

        $itemsPerPage = $this->getParameter('app.shortlink.pagination');
        $user = $this->getUser();

        return $this->repository->findLatest($page, $itemsPerPage, $filterValue, $user);
    }
}
