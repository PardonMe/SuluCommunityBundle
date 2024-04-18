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

use function in_array;
use function preg_quote;
use function str_replace;

/**
 * Represents a single item in the blacklist.
 */
class BlacklistItem
{
    public const TYPE_REQUEST = 'request';
    public const TYPE_BLOCK = 'block';

    private static array $types = [self::TYPE_REQUEST, self::TYPE_BLOCK];

    private ?int $id;

    private ?string $pattern;

    private ?string $regexp;

    private ?string $type;

    public function __construct(?string $pattern = null, ?string $type = null)
    {
        $this->type = $type;

        if (null !== $pattern) {
            $this->setPattern($pattern);
        }
    }

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get pattern.
     */
    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    /**
     * Set pattern.
     */
    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;
        $this->regexp = str_replace('\*', '[^@]*', preg_quote($pattern));

        return $this;
    }

    /**
     * Get regexp.
     */
    public function getRegexp(): ?string
    {
        return $this->regexp;
    }

    /**
     * Get type.
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set type.
     */
    public function setType(string $type): self
    {
        if (!in_array($type, self::$types, true)) {
            throw new InvalidTypeException(self::$types, $type);
        }

        $this->type = $type;

        return $this;
    }
}
