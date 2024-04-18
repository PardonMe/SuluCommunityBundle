<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CommunityBundle\Manager;

use InvalidArgumentException;
use function array_key_exists;
use function sprintf;

class CommunityManagerRegistry implements CommunityManagerRegistryInterface
{
    private array $managers;

    /**
     * @param CommunityManagerInterface[] $managers
     */
    public function __construct(array $managers = [])
    {
        $this->managers = $managers;
    }

    public function get(string $webspaceKey): CommunityManagerInterface
    {
        if (!$this->has($webspaceKey)) {
            throw new InvalidArgumentException(sprintf('Webspace "%s" is not configured.', $webspaceKey));
        }

        return $this->managers[$webspaceKey];
    }

    public function has(string $webspaceKey): bool
    {
        return array_key_exists($webspaceKey, $this->managers);
    }
}
