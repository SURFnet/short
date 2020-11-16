<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\ShortUrl;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\GenerateUniqueShortUrl;

class ShortUrlDataPersister implements ContextAwareDataPersisterInterface
{
    private $entityManager;
    private $generateUniqueShortUrl;

    public function __construct(EntityManagerInterface $entityManager, GenerateUniqueShortUrl $generateUniqueShortUrl)
    {
        $this->entityManager = $entityManager;
        $this->generateUniqueShortUrl = $generateUniqueShortUrl;
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof ShortUrl;
    }
    /**
     * @param User $data
     */
    public function persist($data, array $context = [])
    {
        $shortUrl = $data->getShortUrl() ?? $this->generateUniqueShortUrl->findUniqueShortUrlCode();
        $data->setShortUrl($shortUrl);

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
