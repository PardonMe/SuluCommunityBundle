<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CommunityBundle\Event;

use Sulu\Bundle\CommunityBundle\Manager\CommunityManagerInterface;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event for community actions with config and the user which throw the event.
 *
 * @phpstan-import-type Config from CommunityManagerInterface
 * @phpstan-import-type TypeConfigProperties from CommunityManagerInterface
 */
abstract class AbstractCommunityEvent extends Event
{
    protected User $user;

    /**
     * @var array Config
     */
    protected array $config;

    /**
     * CommunityEvent constructor.
     *
     * @param User $user
     * @param array $config Config
     */
    public function __construct(User $user, array $config)
    {
        $this->user = $user;
        $this->config = $config;
    }

    /**
     * Get user.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Get config.
     *
     * @return Config
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get config property.
     *
     * @template TConfig of string&key-of<Config>
     *
     * @param string $property TConfig
     *
     * @return Config[TTypeConfig]
     */
    public function getConfigProperty(string $property)
    {
        return $this->config[$property];
    }

    /**
     * Get config type property.
     *
     * @template TConfig of string&key-of<Config>
     * @template TTypeConfigProperty of string&key-of<TypeConfigProperties>
     *
     * @param string $type TConfig
     * @param string $property TTypeConfigProperty
     *
     * @return Config[TConfig][TTypeConfigProperty]
     */
    public function getConfigTypeProperty(string $type, string $property)
    {
        return $this->config[$type][$property];
    }
}
