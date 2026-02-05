<?php

/*
 * This file is part of the spiriitlabs/auth-log-bundle package.
 * Copyright (c) SpiriitLabs <https://www.spiriit.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Spiriit\Bundle\AuthLogBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class SpiriitAuthLogExtensionTest extends TestCase
{
    /**
     * Verify that when no transports block is provided,
     * the bundle falls back to sensible defaults so that
     * users can get started without creating a config file.
     */
    public function testEmptyConfigurationUsesDefaults(): void
    {
        $resolved = $this->resolveConfig([]);

        self::assertSame('no-reply@example.com', $resolved['transports']['sender_email']);
        self::assertSame('Security', $resolved['transports']['sender_name']);
        self::assertSame('mailer', $resolved['transports']['mailer']);
        self::assertFalse($resolved['messenger']);
    }

    /**
     * Verify that user-supplied values still take precedence
     * over the built-in defaults.
     */
    public function testCustomTransportsOverrideDefaults(): void
    {
        $resolved = $this->resolveConfig([
            'transports' => [
                'sender_email' => 'alerts@myapp.io',
                'sender_name' => 'My App',
            ],
        ]);

        self::assertSame('alerts@myapp.io', $resolved['transports']['sender_email']);
        self::assertSame('My App', $resolved['transports']['sender_name']);
    }

    /**
     * Helper: run the bundle Configuration tree against
     * a single user-provided config array.
     *
     * @param array<string, mixed> $userConfig
     * @return array<string, mixed>
     */
    private function resolveConfig(array $userConfig): array
    {
        return (new Processor())->processConfiguration(
            new Configuration(),
            [$userConfig],
        );
    }
}
