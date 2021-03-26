<?php


namespace App\EventSubscriber;


use App\Entity\Institution;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Environment;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private const TRAEFIK_PROVIDER_FILE_PATH = '%s/service-%s.yaml';

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
            AfterEntityPersistedEvent::class => ['onAfterEntityPersistedEvent'],
            AfterEntityUpdatedEvent::class => ['onAfterEntityUpdatedEvent'],
            BeforeEntityUpdatedEvent::class => ['onBeforeEntityUpdatedEvent'],
            BeforeEntityDeletedEvent::class => ['onBeforeEntityDeletedEvent'],
        ];
    }

    public function onAfterEntityPersistedEvent(AfterEntityPersistedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Institution) {
            $this->configureTraefikDomain($event->getEntityInstance());
        }
    }

    public function onAfterEntityUpdatedEvent(AfterEntityUpdatedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Institution) {
            $this->configureTraefikDomain($event->getEntityInstance());
        }
    }

    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Institution) {
            $this->deleteTraefikDomain($event->getEntityInstance());
        }
    }

    public function onBeforeEntityDeletedEvent(BeforeEntityDeletedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Institution) {
            $this->deleteTraefikDomain($event->getEntityInstance());
        }
    }

    private function configureTraefikDomain(Institution $institution)
    {
        $filename = $this->getFilename($institution);
        $serviceName = $this->slugger->slug($institution->getDomain());

        $render = $this->twig->render('security/traefik.yaml.twig', [
            'host' => $institution->getDomain(),
            'service' => $serviceName
        ]);

        $this->fs->dumpFile($filename, $render);
    }

    private function deleteTraefikDomain(Institution $institution)
    {
        $filename = $this->getFilename($institution);

        $this->fs->remove($filename);
    }

    private function getFilename(Institution $institution): string
    {
        return sprintf(
            self::TRAEFIK_PROVIDER_FILE_PATH,
            $this->traefikProvidersDirectory,
            $institution->getHash()
        );
    }
}
