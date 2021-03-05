<?php


namespace App\Controller\Security;


use App\Repository\InstitutionRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class InAcademiaStartController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;

    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }

    public function __invoke(Request $request)
    {
        if ($this->getParameter('app.security') !== 'openidc') {
            throw $this->createNotFoundException('Authentication method not available');
        }

        $this->storeTargetPath($request);

        $oAuth2Client = $this->clientRegistry
            ->getClient('inacademia');

        $options = [
            'response_type' => 'id_token',
            'nonce' => md5(mt_rand()),
        ];

        if ($institutionHash = $request->getSession()->get('_return_url')) {
            $options['claims'] = json_encode(["id_token" => ["idp_hint" => ["value" => $institutionHash]]]);
        }

        return $oAuth2Client
            ->redirect([], $options);
    }

    private function storeTargetPath(Request $request): void
    {
        $targetPath = $request->query->get('_target_path');
        if ($targetPath && $request->hasSession() && ($session = $request->getSession()) instanceof SessionInterface) {
            $this->saveTargetPath($session, 'main', $targetPath);
        }
    }
}
