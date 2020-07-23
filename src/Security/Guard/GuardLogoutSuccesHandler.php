<?php


namespace App\Security\Guard;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class GuardLogoutSuccesHandler implements LogoutSuccessHandlerInterface
{
    /**
     * @var string
     */
    private $modAuthMellonLogout;

    public function __construct(string $modAuthMellonLogout)
    {
        $this->modAuthMellonLogout = $modAuthMellonLogout;
    }

    public function onLogoutSuccess(Request $request)
    {
        $isModAuthMellon = $request->getSession()->get('mod_auth_mellon', false);

        if ($isModAuthMellon) {
            return new RedirectResponse($this->modAuthMellonLogout);
        }

        return new RedirectResponse("/");
    }
}
