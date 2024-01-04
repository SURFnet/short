<?php


namespace App\Controller\Manage;

use App\Entity\ShortUrl;
use App\Form\Model\ShortUrlModel;
use App\Form\ShortUrlType;
use App\Message\ShortUrl\UpdateShortUrlMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manage/url/{shortUrl}/edit", name="app_manage_edit", methods={"GET", "POST"})
 * @Security("is_granted('EDIT', instance) or is_granted('ROLE_ADMIN')")
 */
final class EditManageController extends AbstractController
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Request $request, ShortUrl $instance): Response
    {
        if ($instance->getDeleted()) {
            throw $this->createNotFoundException();
        }

        $shortUrl = ShortUrlModel::fromShortUrl($instance);
        $form = $this->createForm(ShortUrlType::class, $shortUrl);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageBus->dispatch(
                new UpdateShortUrlMessage(
                    $instance->getId(),
                    $shortUrl->longUrl,
                    $shortUrl->label
                )
            );

            $this->addFlash('success', 'short_url.updated_successfully');

            return $this->redirectToRoute('app_manage_index');
        }

        return $this->render('manage/edit.html.twig', [
            'short_url' => $instance,
            'form' => $form->createView()
        ]);
    }
}
