<?php

declare(strict_types=1);

namespace App\Controller;

use App\Utility\ApiRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;

class MainController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'app.api_request' => '?' . ApiRequest::class,
            'messenger.default_bus' => '?' . MessageBusInterface::class,
        ]);
    }

    protected function handle(object $message): void
    {
        try {
            $this->dispatchMessage($message);
        } catch (\Exception $e) {
            if (
                $e instanceof HandlerFailedException
                && $e->getPrevious() !== null
            ) {
                throw $e->getPrevious();
            }

            throw $e;
        }
    }

    protected function getApiRequestService(): ApiRequest
    {
        $apiRequest = $this->container->get('app.api_request');

        return $apiRequest instanceof ApiRequest ? $apiRequest : throw new \Exception('Unable to get ApiRequest');
    }

    protected function getApiRequestRequiredField(string $key): mixed
    {
        return $this->getApiRequestService()->getRequiredField($key);
    }

    /**
     * Dispatches a message to the bus.
     *
     * @param object                     $message The message or the message pre-wrapped in an envelope
     * @param array<int, StampInterface> $stamps
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function dispatchMessage(object $message, array $stamps = []): Envelope
    {
        if (!$this->container->has('messenger.default_bus')) {
            $message = class_exists(Envelope::class) ? 'You need to define the "messenger.default_bus" configuration option.' : 'Try running "composer require symfony/messenger".';
            throw new \LogicException('The message bus is not enabled in your application. ' . $message);
        }

        $bus = $this->container->get('messenger.default_bus');
        $bus instanceof MessageBusInterface ?: throw new \Exception("Unable to use 'messenger.default_bus'.");

        return $bus->dispatch($message, $stamps);
    }
}
