<?php


namespace App\Component\Messenger;


use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

trait HandleTrait
{
    use \Symfony\Component\Messenger\HandleTrait { handle as private parentHandle; }

    private function handle($message)
    {
        if (!$this->messageBus instanceof MessageBusInterface) {
            throw new \InvalidArgumentException('The message bus is not set.');
        }

        try {
            return $this->parentHandle($message);
        } catch (HandlerFailedException $e) {
            // unwrap the exception thrown in handler for Symfony Messenger >= 4.3
            while ($e instanceof HandlerFailedException) {
                /** @var \Throwable $e */
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}
