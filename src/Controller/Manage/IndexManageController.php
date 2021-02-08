<?php


namespace App\Controller\Manage;

use App\Component\Messenger\HandleTrait;
use App\Entity\User;
use App\Form\Model\ShortUrlModel;
use App\Form\ShortUrlType;
use App\Message\ShortUrl\CreateShortUrlMessage;
use App\Repository\ShortUrlRepository;
use App\Services\GenerateUniqueShortUrl;
use App\Services\InstitutionalDomainService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manage/", defaults={"page": "1"}, name="app_manage_index", methods={"GET", "POST"})
 * @Route("/manage/page/{page<[1-9]\d*>}", name="app_manage_index_paginated", methods={"GET", "POST"})
 */
final class IndexManageController extends AbstractController
{
    use HandleTrait;

    /**
     * @var ShortUrlRepository
     */
    private $repository;
    /**
     * @var GenerateUniqueShortUrl
     */
    private $generateShortUrlCode;
    /**
     * @var InstitutionalDomainService
     */
    private $institutionalDomainService;

    public function __construct(
        ShortUrlRepository $repository,
        MessageBusInterface $messageBus,
        InstitutionalDomainService $institutionalDomainService
    ) {
        $this->repository = $repository;
        $this->messageBus = $messageBus;
        $this->institutionalDomainService = $institutionalDomainService;
    }

    public function __invoke(Request $request, int $page): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $shortUrl = new ShortUrlModel();

        $form = $this->createForm(ShortUrlType::class, $shortUrl);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $institution = $this->institutionalDomainService->getCurrentInstitution();
            $shortUrl = $this->handle(
                new CreateShortUrlMessage(
                    $user->getId(),
                    $shortUrl->longUrl,
                    null,
                    $institution ? $institution->getDomain() : null
                )
            );

            return $this->redirectToRoute('app_manage_show', ['shortUrl' => $shortUrl->getShortUrl()]);
        }

        $pagination = $this->getPaginationWithSearchFilter($request, $page);

        if ($_SERVER['APP_NEW_UI']) {
            return $this->render(
                'new-ui/manage/index.html.twig',
                [
                    'form' => $form->createView(),
                    'pagination' => $pagination,
                    'route' => 'app_manage_index_paginated',
                ]
            );
        }

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
        $institution = $this->institutionalDomainService->getCurrentInstitution();

        return $this->repository->findLatest(
            $page,
            $itemsPerPage,
            $filterValue,
            $user,
            $institution
        );
    }
}
