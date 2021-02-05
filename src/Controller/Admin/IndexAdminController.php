<?php


namespace App\Controller\Admin;


use App\Component\Messenger\HandleTrait;
use App\Entity\User;
use App\Exception\ShortCodeNotAvailableException;
use App\Form\CustomShortUrlType;
use App\Form\Model\CustomShortUrlModel;
use App\Message\ShortUrl\CreateCustomShortUrlMessage;
use App\Message\ShortUrl\CreateRandomShortUrlMessage;
use App\Message\ShortUrl\CreateShortUrlMessage;
use App\Repository\ShortUrlRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/manage/admin/", defaults={"page": "1"}, name="app_manage_admin", methods={"GET", "POST"})
 * @Route("/manage/admin/page/{page<[1-9]\d*>}", name="app_manage_admin_paginated", methods={"GET"})
 */
final class IndexAdminController extends AbstractController
{
    use HandleTrait;

    /**
     * @var ShortUrlRepository
     */
    private $repository;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ShortUrlRepository $repository, MessageBusInterface $messageBus, TranslatorInterface $translator)
    {
        $this->repository = $repository;
        $this->messageBus = $messageBus;
        $this->translator = $translator;
    }

    public function __invoke(Request $request, int $page): Response
    {
        $customShortUrl = new CustomShortUrlModel();

        $form = $this->createForm(CustomShortUrlType::class, $customShortUrl);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            try {
                $shortUrl = $this->handle(
                    new CreateShortUrlMessage(
                        $user->getId(),
                        $customShortUrl->longUrl,
                        $customShortUrl->shortUrl
                    )
                );

                return $this->redirectToRoute('app_manage_show', ['shortUrl' => $shortUrl->getShortUrl()]);
            } catch (ShortCodeNotAvailableException $e) {
                $errorMessage = $this->translator->trans($e->getMessage(), [], 'validators');

                $form->get('shortUrl')->addError(
                    new FormError($errorMessage)
                );
            }
        }

        $pagination = $this->getPaginationWithSearchFilter($request, $page);

        return $this->render('admin/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            'route' => 'app_manage_admin_paginated',
        ]);
    }

    /**
     * @param Request $request
     * @param int $page
     * @return PaginationInterface
     */
    private function getPaginationWithSearchFilter(Request $request, int $page): PaginationInterface
    {
        $filterValue = $request->query->get('filterValue');
        $itemsPerPage = $this->getParameter('app.shortlink.pagination');

        return $this->repository->findLatest($page, $itemsPerPage, $filterValue);
    }
}
