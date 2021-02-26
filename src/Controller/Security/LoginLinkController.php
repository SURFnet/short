<?php


namespace App\Controller\Security;


use App\Repository\InstitutionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginLinkController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;

    public function __construct(InstitutionRepository $institutionRepository)
    {
        $this->institutionRepository = $institutionRepository;
    }

    public function __invoke(Request $request): Response
    {
        $returnUrl = $request->get('returnUrl');
        $domain = parse_url($returnUrl, PHP_URL_HOST);
        $institution = $this->institutionRepository->findOneBy(['domain' => $domain]);

        if ($institution) {
            $this->saveTargetPath($request->getSession(), 'main', $returnUrl);

            return $this->redirectToRoute('app_manage_index', ['_target_path' => $returnUrl]);
        }

        return $this->redirectToRoute('app_manage_index');
    }
}
