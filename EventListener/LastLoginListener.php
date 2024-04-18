<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CommunityBundle\EventListener;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Last login listener to refresh the users last login timestamp.
 */
class LastLoginListener implements EventSubscriberInterface
{
    protected TokenStorageInterface $tokenStorage;

    protected EntityManager $entityManager;

    protected int $interval;

    /**
     * LastLoginListener constructor.
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManager $entityManager,
        int $interval = 0
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->interval = $interval;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }

    /**
     * Update the last login in specific interval.
     *
     * @param RequestEvent $event
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->interval) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        // Check token authentication availability
        if (!$token) {
            return;
        }

        $user = $token->getUser();

        if (!$user instanceof User || $this->isActiveNow($user)) {
            return;
        }

        $user->setLastLogin(new DateTime());
        $this->entityManager->flush($user);
    }

    /**
     * Check if user was active shortly.
     *
     * @param User $user
     * @return bool
     * @throws Exception
     */
    private function isActiveNow(User $user): bool
    {
        $delay = new DateTime($this->interval . ' seconds ago');

        return $user->getLastLogin() > $delay;
    }
}
