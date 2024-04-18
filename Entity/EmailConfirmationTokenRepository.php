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

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Sulu\Component\Persistence\Repository\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides special queries for email-confirmation-tokens.
 */
class EmailConfirmationTokenRepository extends EntityRepository
{
    /**
     * Return email-confirmation for given token.
     */
    public function findByToken($token)
    {
        /** @var EmailConfirmationToken|null $emailConfirmationToken */
        $emailConfirmationToken = $this->findOneBy(['token' => $token]);

        return $emailConfirmationToken;
    }

    /**
     * Return email-confirmation for given token.
     */
    public function findByUser($user)
    {
        /** @var EmailConfirmationToken|null $emailConfirmationToken */
        $emailConfirmationToken = $this->findOneBy(['user' => $user]);

        return $emailConfirmationToken;
    }
}
