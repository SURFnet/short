<?php


namespace App\EventSubscriber;


use App\Entity\Institution;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Environment;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var SluggerInterface
     */
    private $slugger;
    /**
     * @var string
     */
    private $traefikProvidersDirectory;
    /**
     * @var Filesystem
     */
    private $fs;

    public function __construct(Environment $twig, SluggerInterface $slugger, Filesystem $fs, string $traefikProvidersDirectory)
    {
        $this->twig = $twig;
        $this->slugger = $slugger;
        $this->fs = $fs;
        $this->traefikProvidersDirectory = $traefikProvidersDirectory;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['onBeforeEntityPersistedEvent'],
            BeforeEntityUpdatedEvent::class =>  ['onBeforeEntityUpdatedEvent'],
            BeforeEntityDeletedEvent::class => ['onBeforeEntityDeletedEvent'],
        ];
    }

        public function onBeforeEntityPersistedEvent(BeforeEntityPersistedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Institution) {
            $this->configureTraefikDomain($event->getEntityInstance());
        }
    }

    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Institution) {
            $this->configureTraefikDomain($event->getEntityInstance());
        }
    }

    public function onBeforeEntityDeletedEvent(BeforeEntityDeletedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Institution) {
            $this->deleteTraefikDomain($event->getEntityInstance());
        }
    }

    public function configureTraefikDomain(Institution $institution)
    {
        $filename = sprintf('%s/service-%s.yaml', $this->traefikProvidersDirectory, $institution->getId());
        $serviceName = $this->slugger->slug($institution->getDomain());

        $render = $this->twig->render('security/traefik.yaml.twig', [
            'host' => $institution->getDomain(),
            'service' => $serviceName
        ]);

        $this->fs->dumpFile($filename, $render);
    }

    public function deleteTraefikDomain(Institution $institution)
    {
    }
}
