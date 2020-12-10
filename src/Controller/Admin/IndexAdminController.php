<?php


namespace App\Controller\Admin;


use App\Entity\ShortUrl;
use App\Entity\User;
use App\Exception\ShortCodeNotAvailableException;
use App\Form\ShortUrlType;
use App\Message\ShortUrl\CreateCustomShortUrlMessage;
use App\Message\ShortUrl\CreateRandomShortUrlMessage;
use App\Repository\ShortUrlRepository;
use App\Services\GenerateUniqueShortUrl;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\DelayedMessageHandlingException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
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
        /** @var User $user */
        $user = $this->getUser();

        $shortUrl = new ShortUrl();
        $shortUrl->setOwner($user);

        $form = $this->createForm(ShortUrlType::class, $shortUrl, [
            'is_admin' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $message = $this->generateCreateShortUrlMessage($shortUrl);
                $shortUrl= $this->createShortUrl($message);

                if ($shortUrl) {
                    return $this->redirectToRoute('app_manage_show', ['shortUrl' => $shortUrl->getShortUrl()]);
                }
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

    private function generateCreateShortUrlMessage(ShortUrl $shortUrl)
    {
        if ($shortUrl->getShortUrl()) {
            $message = new CreateCustomShortUrlMessage(
                $shortUrl->getOwner()->getId(),
                $shortUrl->getLongUrl(),
                $shortUrl->getShortUrl()
            );
        } else {
            $message = new CreateRandomShortUrlMessage(
                $shortUrl->getOwner()->getId(),
                $shortUrl->getLongUrl()
            );
        }
        return $message;
    }

    private function createShortUrl(CreateCustomShortUrlMessage $message): array
    {
        try {
            $shortUrl = $this->handle($message);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }

        return $shortUrl;
    }
}
