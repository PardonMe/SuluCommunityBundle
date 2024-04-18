<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CommunityBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint for the BlockedValidator.
 */
class Blocked extends Constraint
{
    public string $message = 'The email "%email%" is blocked.';

    public function validatedBy(): string
    {
        return 'blocked_validator';
    }
}
