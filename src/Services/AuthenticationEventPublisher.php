<?php

/*
 * This file is part of the spiriitlabs/auth-log-bundle package.
 * Copyright (c) SpiriitLabs <https://www.spiriit.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\AuthLogBundle\Services;

use Spiriit\Bundle\AuthLogBundle\AuthenticationLogFactory\PersistableAuthenticationLogFactoryInterface;
use Spiriit\Bundle\AuthLogBundle\Listener\AuthenticationLogEvent;
use Spiriit\Bundle\AuthLogBundle\Listener\AuthenticationLogEvents;
use Spiriit\Bundle\AuthLogBundle\Notification\NotificationInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AuthenticationEventPublisher
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly NotificationInterface $notifier,
    ) {
    }

    public function publish(AuthenticationContext $context): void
    {
        if ($context->authenticationLogFactory instanceof PersistableAuthenticationLogFactoryInterface) {
            $context->authenticationLogFactory->persist($context->userReference, $context->userInformation);
        }

        $event = new AuthenticationLogEvent($context->userReference, $context->userInformation);
        $this->dispatcher->dispatch($event, AuthenticationLogEvents::NEW_DEVICE);

        if (!$event->isLogHandled() && !$context->authenticationLogFactory instanceof PersistableAuthenticationLogFactoryInterface) {
            throw new \Exception('The event must be marked as handled by a listener.');
        }

        $this->notifier->send(
            userInformation: $context->userInformation,
            userReference: $context->userReference
        );
    }
}
