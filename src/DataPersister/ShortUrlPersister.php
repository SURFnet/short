<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\ShortUrl;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\GenerateUniqueShortUrl;

class ShortUrlDataPersister implements DataPersisterInterface
{
    private $entityManager;
    private $generateUniqueShortUrl;

    public function __construct(EntityManagerInterface $entityManager, GenerateUniqueShortUrl $generateUniqueShortUrl)
    {
        $this->entityManager = $entityManager;
        $this->generateUniqueShortUrl = $generateUniqueShortUrl;
    }
    public function supports($data): bool
    {
        return $data instanceof ShortUrl;
    }
    /**
     * @param User $data
     */
    public function persist($data)
    {
        $shortUrl = $data->getShortUrl() ?? $this->generateUniqueShortUrl->findUniqueShortUrlCode();
        $data->setShortUrl($shortUrl);

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
