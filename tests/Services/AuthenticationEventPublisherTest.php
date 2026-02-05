<?php

/*
 * This file is part of the spiriitlabs/auth-log-bundle package.
 * Copyright (c) SpiriitLabs <https://www.spiriit.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\Tests\Services;

use PHPUnit\Framework\TestCase;
use Spiriit\Bundle\AuthLogBundle\AuthenticationLogFactory\AuthenticationLogFactoryInterface;
use Spiriit\Bundle\AuthLogBundle\AuthenticationLogFactory\PersistableAuthenticationLogFactoryInterface;
use Spiriit\Bundle\AuthLogBundle\DTO\UserReference;
use Spiriit\Bundle\AuthLogBundle\FetchUserInformation\UserInformation;
use Spiriit\Bundle\AuthLogBundle\Notification\NotificationInterface;
use Spiriit\Bundle\AuthLogBundle\Services\AuthenticationContext;
use Spiriit\Bundle\AuthLogBundle\Services\AuthenticationEventPublisher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AuthenticationEventPublisherTest extends TestCase
{
    private function buildContext(
        AuthenticationLogFactoryInterface $logFactory,
    ): AuthenticationContext {
        $ref = new UserReference(type: 'account', id: '42');
        $ref->setNotificationParameters('alice@acme.dev', 'Alice');

        $info = new UserInformation(
            '10.0.0.1',
            'TestAgent/1.0',
            new \DateTimeImmutable('2026-01-15'),
            null,
        );

        return new AuthenticationContext(
            authenticationLogFactory: $logFactory,
            userReference: $ref,
            userInformation: $info,
        );
    }

    /**
     * A factory that implements PersistableAuthenticationLogFactoryInterface
     * should have its persist() called and the notification should still be sent,
     * even without a manual event listener calling markAsHandled().
     */
    public function testPublishCallsPersistOnPersistableFactory(): void
    {
        $mockFactory = $this->createMock(PersistableAuthenticationLogFactoryInterface::class);
        $mockFactory->expects(self::once())->method('persist');

        $ctx = $this->buildContext($mockFactory);

        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockNotifier   = $this->createMock(NotificationInterface::class);
        $mockNotifier->expects(self::once())->method('send');

        $sut = new AuthenticationEventPublisher($mockDispatcher, $mockNotifier);
        $sut->publish($ctx);
    }

    /**
     * When the factory does NOT implement PersistableAuthenticationLogFactoryInterface
     * and no listener marks the event as handled, we still expect the original
     * exception to be thrown.
     */
    public function testPublishThrowsWhenLegacyFactoryHasNoListener(): void
    {
        $plainFactory = $this->createMock(AuthenticationLogFactoryInterface::class);

        $ctx = $this->buildContext($plainFactory);

        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockNotifier   = $this->createMock(NotificationInterface::class);
        $mockNotifier->expects(self::never())->method('send');

        $sut = new AuthenticationEventPublisher($mockDispatcher, $mockNotifier);

        $this->expectException(\Exception::class);
        $sut->publish($ctx);
    }
}
