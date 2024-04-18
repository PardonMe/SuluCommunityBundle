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
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles registration, confirmation, password reset and forget.
 *
 * @phpstan-type TypeConfigProperties array{
 *      enabled: bool,
 *      template: string,
 *      service: string|null,
 *      embed_template: string,
 *      type: string,
 *      options: array,
 *      activate_user: bool,
 *      auto_login: bool,
 *      redirect_to: string|null,
 *      email: array{
 *          subject: string,
 *          admin_template: string|null,
 *          user_template: string|null,
 *      },
 *      delete_user: bool,
 * }
 *
 * @phpstan-type Config array{
 *     from: string|string[],
 *     to: string|string[],
 *     webspace_key: string,
 *     role: string,
 *     firewall: string,
 *     maintenance: array{
 *         enabled: bool,
 *         template: string,
 *     },
 *     login: TypeConfigProperties,
 *     registration: TypeConfigProperties,
 *     completion: TypeConfigProperties,
 *     confirmation: TypeConfigProperties,
 *     password_forget: TypeConfigProperties,
 *     password_reset: TypeConfigProperties,
 *     profile: TypeConfigProperties,
 *     blacklisted: TypeConfigProperties,
 *     blacklist_confirmed: TypeConfigProperties,
 *     blacklist_denied: TypeConfigProperties,
 *     email_confirmation: TypeConfigProperties,
 * }
 */
interface CommunityManagerInterface
{
    /**
     * Return the webspace key.
     */
    public function getWebspaceKey(): string;

    /**
     * Register user for the system.
     */
    public function register(User $user): User;

    /**
     * Complete the user registration.
     */
    public function completion(User $user): User;

    /**
     * Login user into the system.
     */
    public function login(User $user, Request $request): void;

    /**
     * Confirm the user registration.
     */
    public function confirm(string $token): ?User;

    /**
     * Generate password reset token and save.
     */
    public function passwordForget(string $emailUsername): ?User;

    /**
     * Reset user password token.
     */
    public function passwordReset(User $user): User;

    /**
     * Get community webspace config.
     *
     * @return Config
     */
    public function getConfig(): array;

    /**
     * Get community webspace config property.
     *
     * @template TConfig of string&key-of<Config>
     *
     * @param string $property TConfig
     *
     * @return Config[TTypeConfig]
     * @throws InvalidArgumentException
     */
    public function getConfigProperty(string $property): mixed;

    /**
     * Get community webspace config type property.
     *
     * @template TConfig of string&key-of<Config>
     * @template TTypeConfigProperty of string&key-of<TypeConfigProperties>
     *
     * @param string $type TConfig
     * @param string $property TTypeConfigProperty
     *
     * @return Config[TConfig][TTypeConfigProperty]
     * @throws InvalidArgumentException
     */
    public function getConfigTypeProperty(string $type, string $property): mixed;

    /**
     * Send email to user and admin by type.
     */
    public function sendEmails(string $type, User $user): void;

    /**
     * Save profile for given user.
     *
     * @param User $user
     * @return User|null
     */
    public function saveProfile(User $user): ?User;
}
