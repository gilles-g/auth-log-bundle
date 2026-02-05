<?php

declare(strict_types=1);

/*
 * This file is part of the spiriitlabs/auth-log-bundle package.
 * Copyright (c) SpiriitLabs <https://www.spiriit.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\AuthLogBundle\AuthenticationLogFactory;

use Spiriit\Bundle\AuthLogBundle\DTO\UserReference;
use Spiriit\Bundle\AuthLogBundle\FetchUserInformation\UserInformation;

/**
 * Extends AuthenticationLogFactoryInterface with a persist() method,
 * allowing the bundle to automatically persist authentication logs
 * without requiring a separate event listener.
 */
interface PersistableAuthenticationLogFactoryInterface extends AuthenticationLogFactoryInterface
{
    public function persist(UserReference $userReference, UserInformation $userInformation): void;
}
