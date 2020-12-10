<?php


namespace App\Services;


use App\Entity\ShortUrl;
use App\Entity\User;
use App\Exception\ShortCodeNotAvailableException;
use App\Exception\ShortUserException;
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
     * @var string
     */
    private $shortcodeChars;
    /**
     * @var int
     */
    private $shortcodeLength;
    /**
     * @var int
     */
    private $shortcodeMaxTries;

    public function __construct(EntityManagerInterface  $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->shortcodeChars = $parameterBag->get('app.shortcode.chars');
        $this->shortcodeLength = (int) $parameterBag->get('app.shortcode.length');
        $this->shortcodeMaxTries = (int) $parameterBag->get('app.shortcode.maxtries');

    }

    public function getUniqueShortUrlCode(): string
    {
        for ($i = 0 ; $i < $this->shortcodeMaxTries; $i++) {
            $code = $this->generateCode();

            if ($this->checkShortUrlCodeIsAvailable($code)) {
                return $code;
            }
        }

        throw ShortCodeNotAvailableException::becauseTooManyTries();
    }

    public function checkShortUrlCodeIsAvailable(string $code): bool
    {
        $query = $this->entityManager->createQuery('SELECT o FROM \App\Entity\ShortUrl o WHERE o.shortUrl = :code');
        $query->setParameter('code', $code);
        $query->setLockMode(LockMode::PESSIMISTIC_WRITE);

        return $query->getOneOrNullResult() === null;
    }

    private function generateCode() : string
    {
        $shortCodeCharsLength = strlen($this->shortcodeChars);
        $code = "";

        for($i=0; $i < $this->shortcodeLength; ++$i) {
            $code .= $this->shortcodeChars[mt_rand(0, $shortCodeCharsLength - 1)];
        }

        return $code;
    }
}
