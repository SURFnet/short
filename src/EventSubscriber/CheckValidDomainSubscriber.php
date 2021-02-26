<?php

namespace App\EventSubscriber;

use App\Repository\InstitutionRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CheckValidDomainSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $appURLdomain;
    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;

    public function __construct(ParameterBagInterface $parameterBag, InstitutionRepository $institutionRepository)
    {
        $this->appURLdomain = $parameterBag->get('app.urldomain');
        $this->institutionRepository = $institutionRepository;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $host = $event->getRequest()->getHost();

        if ($host === $this->appURLdomain) {
            return;
        }

        if ($this->institutionRepository->findOneBy(['domain' => $host])) {
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
