<?php


namespace App\Controller\Security;


use App\Entity\Institution;
use App\Services\InstitutionalDomainService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/login", name="login", host="%app.urldomain%")
 * @Route("/login", name="institutional_login")
 */
class LoginController extends AbstractController
{
    /**
     * @var InstitutionalDomainService
     */
    private $institutionalDomainService;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(InstitutionalDomainService $institutionalDomainService, TokenStorageInterface $tokenStorage)
    {
        $this->institutionalDomainService = $institutionalDomainService;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request)
    {
        $this->tokenStorage->setToken(null);

        if ($this->institutionalDomainService->isMainDomain()) {
            return $this->redirectToRoute('app_manage_index');
        }

        $institution = $this->institutionalDomainService->getCurrentInstitution();

        if (!$institution instanceof Institution) {
            throw new NotFoundHttpException();
        }

        return $this->redirectToRoute('login', [
            'return_url' => $institution->getHash(),
        ]);
    }
}
