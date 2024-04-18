<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CommunityBundle\Entity;

use InvalidArgumentException;
use function implode;
use function sprintf;

/**
 * Invalid type given.
 */
class InvalidTypeException extends InvalidArgumentException
{

    private array $validTypes;

    private string $type;

    public function __construct(array $validTypes, string $type)
    {
        parent::__construct(
            sprintf('Invalid type "%s" given. Valid types are [%s]', $type, implode(', ', $validTypes)),
            10000
        );

        $this->validTypes = $validTypes;
        $this->type = $type;
    }

    public function getValidTypes(): array
    {
        return $this->validTypes;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
