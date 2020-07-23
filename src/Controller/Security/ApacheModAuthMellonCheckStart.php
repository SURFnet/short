<?php


namespace App\Controller\Security;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class ApacheModAuthMellonCheckStart extends AbstractController
{
    use TargetPathTrait;

    public function __invoke(Request $request)
    {
        $this->storeTargetPath($request);

        return $this->redirectToRoute('connect_mellon_check');
    }

    private function storeTargetPath(Request $request): void
    {
        $targetPath = $request->query->get('_target_path');
        if ($targetPath && $request->hasSession() && ($session = $request->getSession()) instanceof SessionInterface) {
            $this->saveTargetPath($session, 'main', $targetPath);
        }
    }
}
