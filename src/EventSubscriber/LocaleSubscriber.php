<?php


namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $locales;

    public function __construct(array $locales, string $defaultLocale)
    {
        $this->locales = array_keys($locales);

        if (!in_array($defaultLocale, $this->locales, true)) {
            $validLocales = implode(", ", $this->locales);

            throw new \UnexpectedValueException(sprintf('The default locale ("%s") must be one of "%s".', $defaultLocale, $validLocales));
        }

        // Add the default locale at the first position of the array,
        // because Symfony\HttpFoundation\Request::getPreferredLanguage
        // returns the first element when no an appropriate language is found
        array_unshift($this->locales, $defaultLocale);
        $this->locales = array_unique($this->locales);
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }


        if ('undefined' === $request->getLocale()) {
            if ($locale = $request->getSession()->get('_locale')) {
                $request->setLocale($locale);
            } else {
                $preferredLanguage = $request->getPreferredLanguage($this->locales);
                $request->setLocale($preferredLanguage);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
