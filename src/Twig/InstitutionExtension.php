<?php

namespace App\Twig;

use App\Entity\Institution;
use App\Repository\InstitutionRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class InstitutionExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;

    public function __construct(RequestStack $requestStack, InstitutionRepository $institutionRepository)
    {
        $this->requestStack = $requestStack;
        $this->institutionRepository = $institutionRepository;
    }

    public function getGlobals(): array
    {
        return [
            'institution' => $this->getCurrentInstitution(),
        ];
    }

    private function getCurrentInstitution(): ?Institution
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        $host = $request->getHost();

        return $this->institutionRepository->findOneBy(['domain' => $host]);
    }
}
