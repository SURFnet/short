<?php


namespace App\Services;


use App\Entity\Institution;
use App\Repository\InstitutionRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class InstitutionalDomainService
{
    /**
     * @var string
     */
    private $appURLdomain;
    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        InstitutionRepository $institutionRepository,
        ParameterBagInterface $parameterBag,
        RequestStack $requestStack
    )
    {
        $this->appURLdomain = $parameterBag->get('app.urldomain');
        $this->institutionRepository = $institutionRepository;
        $this->requestStack = $requestStack;
    }

    public function getMainDomain(): string
    {
        return $this->appURLdomain;
    }

    public function isMainDomain(): bool
    {
        if (!$request = $this->requestStack->getMasterRequest()) {
            return false;
        }

        return $request->getHost() === $this->appURLdomain;
    }

    public function getCurrentInstitution(): ?Institution
    {
        if (!$request = $this->requestStack->getMasterRequest()) {
            return null;
        }

        return $this->institutionRepository->findOneBy(['domain' => $request->getHost()]);
    }

    public function getCurrentDomain(): string
    {
        if (!$this->getCurrentInstitution()) {
            return $this->getMainDomain();
        }

        return $this->getCurrentInstitution()->getDomain();
    }
}
