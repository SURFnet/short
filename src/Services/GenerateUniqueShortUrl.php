<?php


namespace App\Services;


use App\Entity\ShortUrl;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class GenerateUniqueShortUrl
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(EntityManagerInterface  $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
    }

    public function generate(string $longUrl, string $owner): ShortUrl
    {
        $this->entityManager->beginTransaction();
        try {
            $shortUrl = new ShortUrl();
            $shortUrl->setLongUrl($longUrl);
            $shortUrl->setOwner($owner);

            $code = $this->findUniqueShortUrlCode();
            $shortUrl->setShortUrl($code);

            $this->entityManager->persist($shortUrl);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }

        return $shortUrl;
    }

    private function findUniqueShortUrlCode(): string
    {
        $shortcodeMaxTries = $this->parameterBag->get('app.shortcode.maxtries');

        for ($i = 0 ; $i < $shortcodeMaxTries; $i++) {
            $code = $this->generateCode();

            $query = $this->entityManager->createQuery('SELECT o FROM \App\Entity\ShortUrl o WHERE o.shortUrl = :code');
            $query->setParameter('code', $code);
            $query->setLockMode(LockMode::PESSIMISTIC_WRITE);

            if (null === $query->getOneOrNullResult()) {
                return $code;
            }
        }

        // TODO: Catch exception in controller
        throw new \RuntimeException("Too many tries to create shortcode");
    }

    public function generateCode() : string
    {
        $shortcodeChars = $this->parameterBag->get('app.shortcode.chars');
        $shortcodeLength = $this->parameterBag->get('app.shortcode.length');

        $code = "";

        for($i=0; $i < $this->$shortcodeLength; ++$i) {
            $code .= $this->$shortcodeChars[mt_rand(0, strlen($shortcodeChars) - 1)];
        }
        return $code;
    }
}
