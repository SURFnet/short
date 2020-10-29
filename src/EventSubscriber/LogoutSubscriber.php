<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $appSecurityLogout;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->appSecurityLogout = $parameterBag->get('app.security.logout');
    }

    public function onLogoutEvent(LogoutEvent $event)
    {
        $event->setResponse(new RedirectResponse($this->appSecurityLogout));
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
