<?php

/*
 * This file is part of the spiriitlabs/auth-log-bundle package.
 * Copyright (c) SpiriitLabs <https://www.spiriit.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\Tests\AuthenticationLogFactory;

use PHPUnit\Framework\TestCase;
use Spiriit\Bundle\AuthLogBundle\AuthenticationLogFactory\AuthenticationLogFactoryInterface;
use Spiriit\Bundle\AuthLogBundle\AuthenticationLogFactory\FetchAuthenticationLogFactory;
use Spiriit\Bundle\AuthLogBundle\DTO\UserReference;
use Spiriit\Bundle\AuthLogBundle\FetchUserInformation\UserInformation;

class FetchAuthenticationLogFactoryTest extends TestCase
{
    public function testCreateFromReturnsCorrectFactory(): void
    {
        $factory1 = $this->createMock(AuthenticationLogFactoryInterface::class);
        $factory1->method('supports')->willReturn('type1');

        $factory2 = $this->createMock(AuthenticationLogFactoryInterface::class);
        $factory2->method('supports')->willReturn('type2');

        $fetchFactory = new FetchAuthenticationLogFactory([$factory1, $factory2]);

        $result = $fetchFactory->createFrom('type2');

        self::assertSame($factory2, $result);
    }

    public function testCreateFromThrowsExceptionWhenFactoryNotFound(): void
    {
        $factory1 = $this->createMock(AuthenticationLogFactoryInterface::class);
        $factory1->method('supports')->willReturn('type1');

        $fetchFactory = new FetchAuthenticationLogFactory([$factory1]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('There is no authentication log factory available named unknown');

        $fetchFactory->createFrom('unknown');
    }

    public function testCreateFromCachesFactoryMap(): void
    {
        // Create a mock that should only call supports() once per factory
        $factory1 = $this->createMock(AuthenticationLogFactoryInterface::class);
        $factory1->expects(self::once())
            ->method('supports')
            ->willReturn('type1');

        $fetchFactory = new FetchAuthenticationLogFactory([$factory1]);

        // First call builds the map
        $result1 = $fetchFactory->createFrom('type1');
        // Second call should use cached map, not call supports() again
        $result2 = $fetchFactory->createFrom('type1');

        self::assertSame($factory1, $result1);
        self::assertSame($factory1, $result2);
    }
}
