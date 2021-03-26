<?php

namespace App\Twig;

use App\Entity\Institution;
use App\Entity\ShortUrl;
use App\Repository\InstitutionRepository;
use App\Services\InstitutionalDomainService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

final class InstitutionExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var InstitutionalDomainService
     */
    private $institutionalDomainService;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    public function __construct(InstitutionalDomainService $institutionalDomainService, UrlGeneratorInterface $generator)
    {
        $this->institutionalDomainService = $institutionalDomainService;
        $this->generator = $generator;
    }

    public function getGlobals(): array
    {
        return [
            'institution' => $this->institutionalDomainService->getCurrentInstitution(),
            'domain' => $this->institutionalDomainService->getCurrentDomain(),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('short_path', [$this, 'getShortPath']),
            new TwigFunction('preview_short_path', [$this, 'getPreviewShortPath']),
            new TwigFunction('qr_short_path', [$this, 'getQrShortPath']),
        ];
    }

    public function getShortPath(ShortUrl $shortUrl, bool $schemeRelative = false): string
    {
        $route = 'redirect';
        $req = sprintf('%s', $shortUrl->getShortUrl());

        return ltrim($this->getPath($req, $route, $schemeRelative), '/');
    }

    public function getPreviewShortPath(ShortUrl $shortUrl, bool $schemeRelative = false): string
    {
        $route = 'preview';
        $req = sprintf('%s+', $shortUrl->getShortUrl());

        return ltrim($this->getPath($req, $route, $schemeRelative), '/');
    }

    public function getQrShortPath(ShortUrl $shortUrl, bool $schemeRelative = false): string
    {
        $route = 'quickresponse';
        $req = sprintf('%s~', $shortUrl->getShortUrl());

        return ltrim($this->getPath($req, $route, $schemeRelative), '/');
    }

    private function getPath(string $req, string $route, bool $schemeRelative): string
    {
        $parameters = ['domain' => $this->institutionalDomainService->getCurrentDomain(), "req" => $req];

        return $this->generator->generate($route, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
