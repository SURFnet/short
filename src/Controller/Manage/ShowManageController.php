<?php


namespace App\Controller\Manage;


use App\Entity\ShortUrl;
use App\Form\Model\ShortUrlModel;
use App\Form\ShortUrlType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/manage/url/{shortUrl}/show", name="app_manage_show", methods={"GET"})
 * @IsGranted("SHOW", subject="instance")
 */
final class ShowManageController extends AbstractController
{
    public function __invoke(ShortUrl $instance): Response
    {
        if ($instance->getDeleted()) {
            throw $this->createNotFoundException();
        }

        $shortUrl = ShortUrlModel::fromShortUrl($instance);
        $form = $this->createForm(ShortUrlType::class, $shortUrl);

        return $this->render('manage/show.html.twig', [
            'short_url' => $instance,
            'form' => $form->createView(),
        ]);
    }
}
