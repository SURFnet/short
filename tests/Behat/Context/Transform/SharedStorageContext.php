<?php


namespace App\Tests\Behat\Context\Transform;


use App\Tests\Behat\Service\SharedStorage;
use Behat\Behat\Context\Context;

class SharedStorageContext implements Context
{
    /**
     * @var SharedStorage
     */
    private $sharedStorage;

    public function __construct(SharedStorage $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform /^(it|its|theirs|them)$/
     */
    public function getLatestResource()
    {
        return $this->sharedStorage->getLatestResource();
    }
}
