<?php

/*
 * This file is part of the spiriitlabs/auth-log-bundle package.
 * Copyright (c) SpiriitLabs <https://www.spiriit.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\AuthLogBundle\AuthenticationLogFactory;

class FetchAuthenticationLogFactory
{
    /**
     * @var array<string, AuthenticationLogFactoryInterface>
     */
    private array $factoryMap = [];

    /**
     * @param AuthenticationLogFactoryInterface[] $authenticationLogFactories
     */
    public function __construct(
        private readonly iterable $authenticationLogFactories,
    ) {
    }

    public function createFrom(string $factorySupport): AuthenticationLogFactoryInterface
    {
        if ([] === $this->factoryMap) {
            $this->buildFactoryMap();
        }

        if (!isset($this->factoryMap[$factorySupport])) {
            throw new \InvalidArgumentException('There is no authentication log factory available named '.$factorySupport);
        }

        return $this->factoryMap[$factorySupport];
    }

    private function buildFactoryMap(): void
    {
        foreach ($this->authenticationLogFactories as $authenticationLogFactory) {
            $this->factoryMap[$authenticationLogFactory->supports()] = $authenticationLogFactory;
        }
    }
}
