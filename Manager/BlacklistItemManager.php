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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Sulu\Bundle\CommunityBundle\Entity\BlacklistItem;
use Sulu\Bundle\CommunityBundle\Entity\BlacklistItemRepository;
use function is_array;

/**
 * Manages blacklist-items.
 */
class BlacklistItemManager implements BlacklistItemManagerInterface
{
    private EntityManagerInterface $entityManager;

    private BlacklistItemRepository $blacklistItemRepository;

    public function __construct(EntityManagerInterface $entityManager, BlacklistItemRepository $blacklistItemRepository)
    {
        $this->entityManager = $entityManager;
        $this->blacklistItemRepository = $blacklistItemRepository;
    }

    public function find(int $id): BlacklistItem
    {
        /** @var BlacklistItem $blacklistItem */
        $blacklistItem = $this->blacklistItemRepository->find($id);

        return $blacklistItem;
    }

    public function create(): BlacklistItem
    {
        $item = $this->blacklistItemRepository->createNew();

        $this->entityManager->persist($item);

        return $item;
    }

    /**
     * @param array|int $ids
     * @return void
     * @throws ORMException
     */
    public function delete(array|int $ids): void
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        foreach ($ids as $id) {
            /** @var BlacklistItem $object */
            $object = $this->entityManager->getReference($this->blacklistItemRepository->getClassName(), $id);

            $this->entityManager->remove($object);
        }
    }
}
