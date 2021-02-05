<?php


namespace App\Controller\Manage;


use App\Entity\ShortUrl;
use App\Message\ShortUrl\DeleteShortUrlMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manage/url/{shortUrl}/delete", name="app_manage_delete", methods={"POST"})
 * @Security("is_granted('DELETE', instance) or is_granted('ROLE_ADMIN')")
 */
final class DeleteManageController extends AbstractController
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Request $request, ShortUrl $instance)
    {
        if ($instance->getDeleted()) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('app_manage_index');
        }

        $this->messageBus->dispatch(
            new DeleteShortUrlMessage(
                $instance->getId()
            )
        );

        $this->addFlash('success', 'short_url.deleted_successfully');

        return $this->redirectToRoute('app_manage_index');
    }
}
