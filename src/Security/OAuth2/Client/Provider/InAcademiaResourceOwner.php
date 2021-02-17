<?php


namespace App\Security\OAuth2\Client\Provider;


use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class InAcademiaResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /**
     * @var array
     */
    private $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        $issuer = $this->getValueByKey($this->response, 'iss');

        return sprintf('%s@%s',
            $this->getValueByKey($this->response, 'sub'),
            parse_url($issuer, PHP_URL_HOST)
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->response;
    }
}
