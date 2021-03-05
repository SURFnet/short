<?php


namespace App\EventSubscriber;


use App\Repository\InstitutionRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;

    public function __construct(InstitutionRepository $institutionRepository)
    {
        $this->institutionRepository = $institutionRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->get('_route') !== 'login') {
            return;
        }

        $institutionHash = $request->get('return_url');

        if (!$institutionHash) {
            $request->getSession()->remove('_return_url');

            return;
        }

        if (!$institution = $this->institutionRepository->findOneBy(['hash' => $institutionHash])) {
            throw new BadRequestException('Invalid return url');
        }

        $request->getSession()->set('_return_url', $institutionHash);
    }
}
