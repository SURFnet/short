<?php


namespace App\Controller\Manage;

use App\Entity\ShortUrl;
use App\Form\ShortUrlType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manage/{shortUrl}/edit", name="app_manage_edit", methods={"GET", "POST"})
 * @Security("is_granted('EDIT', instance) or is_granted('ROLE_ADMIN')")
 */
final class EditManageController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, ShortUrl $instance): Response
    {
        if ($instance->getDeleted()) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted();

        $form = $this->createForm(ShortUrlType::class, $instance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $instance->setUpdated();
            $this->entityManager->flush();

            $this->addFlash('success', 'short_url.updated_successfully');

            return $this->redirectToRoute('app_manage_index');
        }

        return $this->render('manage/edit.html.twig', [
            'short_url' => $instance,
            'form' => $form->createView()
        ]);
    }
}
