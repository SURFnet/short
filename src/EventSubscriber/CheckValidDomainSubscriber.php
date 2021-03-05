<?php

namespace App\EventSubscriber;

use App\Entity\Institution;
use App\Services\InstitutionalDomainService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CheckValidDomainSubscriber implements EventSubscriberInterface
{
    /**
     * @var InstitutionalDomainService
     */
    private $institutionalDomainService;

    public function __construct(InstitutionalDomainService $institutionalDomainService)
    {
        $this->institutionalDomainService = $institutionalDomainService;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->institutionalDomainService->isMainDomain()) {
            return;
        }

        if ($this->institutionalDomainService->getCurrentInstitution() instanceof Institution) {
            return;
        }

        throw new NotFoundHttpException('Not recognized domain');
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
