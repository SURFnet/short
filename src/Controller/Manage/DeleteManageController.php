<?php


namespace App\Controller\Manage;


use App\Entity\ShortUrl;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manage/url/{shortUrl}/delete", name="app_manage_delete", methods={"POST"})
 * @Security("is_granted('DELETE', instance) or is_granted('ROLE_ADMIN')")
 */
final class DeleteManageController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, ShortUrl $instance)
    {
        if ($instance->getDeleted()) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('app_manage_index');
        }

        $instance->setDeleted(true);
        $instance->setUpdated();
        $this->entityManager->flush();

        $this->addFlash('success', 'short_url.deleted_successfully');

        return $this->redirectToRoute('app_manage_index');
    }
}
