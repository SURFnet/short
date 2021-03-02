<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\ShortUrl;
use App\Message\ShortUrl\CreateShortUrlMessage;
use App\Entity\User;
use App\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;


final class ShortUrlDataPersister implements ContextAwareDataPersisterInterface
{
    use HandleTrait;

    public function __construct(ContextAwareDataPersisterInterface $decorated, MessageBusInterface $messageBus)
    {
        $this->decorated = $decorated;
        $this->messageBus = $messageBus;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->decorated->supports($data, $context);
    }

    public function persist($data, array $context = [])
    {
        // So we have something to return?
        $result = new ShortUrl();

        if ($data instanceof ShortUrl) {
            if (
                ($context['collection_operation_name'] ?? null) === 'post'
                )
            {
                // Create ShortUrl
                $result = $this->handle(
                        new CreateShortUrlMessage(
                        // TODO We need valid user from API token
                        'test',
                        $data->getLongUrl(),
                        null
                    )
                );
            }
            else
            {
                $data->setUpdated();
                $result = $this->decorated->persist($data, $context);
            }

        }
        return $result;
    }

    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }


}
