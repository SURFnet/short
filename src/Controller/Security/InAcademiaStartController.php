<?php


namespace App\Controller\Security;


use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class InAcademiaStartController extends AbstractController
{
    use TargetPathTrait;

    public function __invoke(Request $request, ClientRegistry $clientRegistry)
    {
        if ($this->getParameter('app.security') !== 'openidc') {
            throw $this->createNotFoundException('Authentication method not available');
        }

        $this->storeTargetPath($request);

        $oAuth2Client = $clientRegistry
            ->getClient('inacademia');

        $redirectResponse = $oAuth2Client
            ->redirect([], [
                'response_type' => 'id_token',
                'nonce' => md5(mt_rand()),
            ]);

        return $redirectResponse;
    }

    private function storeTargetPath(Request $request): void
    {
        $targetPath = $request->query->get('_target_path');
        if ($targetPath && $request->hasSession() && ($session = $request->getSession()) instanceof SessionInterface) {
            $this->saveTargetPath($session, 'main', $targetPath);
        }
    }
}
