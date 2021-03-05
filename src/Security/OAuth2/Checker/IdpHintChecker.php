<?php


namespace App\Security\OAuth2\Checker;


use Jose\Component\Checker\ClaimChecker;
use Jose\Component\Checker\HeaderChecker;
use Jose\Component\Checker\InvalidClaimException;
use Jose\Component\Checker\InvalidHeaderException;

final class IdpHintChecker implements ClaimChecker, HeaderChecker
{
    private const CLAIM_NAME = 'idp_hint';

    /**
     * @var string
     */
    private $idpHint;
    /**
     * @var bool
     */
    private $protectedHeader = false;

    public function __construct(?string $idpHint,  bool $protectedHeader = false)
    {
        $this->idpHint = $idpHint;
        $this->protectedHeader = $protectedHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function checkClaim($value): void
    {
        $this->checkValue($value, InvalidClaimException::class);
    }

    /**
     * {@inheritdoc}
     */
    public function checkHeader($value): void
    {
        $this->checkValue($value, InvalidHeaderException::class);
    }

    public function supportedClaim(): string
    {
        return self::CLAIM_NAME;
    }

    public function supportedHeader(): string
    {
        return self::CLAIM_NAME;
    }

    public function protectedHeaderOnly(): bool
    {
        return $this->protectedHeader;
    }

    private function checkValue($value, string $class): void
    {
        if (is_string($value) && $value !== $this->idpHint) {
            throw new $class('Bad idp_hint.', self::CLAIM_NAME, $value);
        }

        if (empty($value) && !empty($this->idpHint)) {
            throw new $class('Bad idp_hint.', self::CLAIM_NAME, $value);
        }
    }
}
