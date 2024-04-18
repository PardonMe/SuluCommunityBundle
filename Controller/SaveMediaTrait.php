<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CommunityBundle\Controller;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sulu\Bundle\MediaBundle\Api\Media;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Sulu\Component\Media\SystemCollections\SystemCollectionManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function is_array;

trait SaveMediaTrait
{
    /**
     * @param FormInterface $form
     * @param User $user
     * @param string $locale
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function saveMediaFields(FormInterface $form, User $user, string $locale): void
    {
        $this->saveAvatar($form, $user, $locale);
        $this->saveDocuments($form, $user, $locale);
    }

    /**
     * @param FormInterface $form
     * @param User $user
     * @param string $locale
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function saveDocuments(FormInterface $form, User $user, string $locale): array
    {
        if (!$form->has('medias')) {
            return [];
        }

        $uploadedFiles = $form->get('medias')->getData();

        if (empty($uploadedFiles)) {
            return [];
        }

        if (!is_array($uploadedFiles)) {
            $uploadedFiles = [$uploadedFiles];
        }

        $contact = $user->getContact();
        $apiMedias = [];

        foreach ($uploadedFiles as $uploadedFile) {
            $apiMedia = $this->saveMedia($uploadedFile, null, $locale, $user->getId());
            $apiEntity = $apiMedia->getEntity();
            if ($apiEntity instanceof MediaInterface) {
                $contact->addMedia($apiEntity);
            }

            $apiMedias[] = $apiMedia;
        }

        return $apiMedias;
    }

    /**
     * @param FormInterface $form
     * @param User $user
     * @param string $locale
     * @return Media|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function saveAvatar(FormInterface $form, User $user, string $locale): ?Media
    {
        if (!$form->has('avatar')) {
            return null;
        }

        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $form->get('avatar')->getData();
        if (null === $uploadedFile) {
            return null;
        }

        $avatar = $user->getContact()->getAvatar();

        /** @var MediaInterface|null $avatar */
        $apiMedia = $this->saveMedia($uploadedFile, $avatar?->getId(), $locale, $user->getId());
        $apiEntity = $apiMedia->getEntity();
        if ($apiEntity instanceof MediaInterface) {
            $user->getContact()->setAvatar($apiEntity);
        }

        return $apiMedia;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param int|null $id
     * @param string $locale
     * @param int|null $userId
     * @return Media
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function saveMedia(UploadedFile $uploadedFile, ?int $id, string $locale, ?int $userId): Media
    {
        return $this->getMediaManager()->save(
            $uploadedFile,
            [
                'id' => $id,
                'locale' => $locale,
                'title' => $uploadedFile->getClientOriginalName(),
                'collection' => $this->getContactMediaCollection(),
            ],
            $userId
        );
    }

    /**
     * Get system collection manager.
     *
     * @return SystemCollectionManagerInterface|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getSystemCollectionManager(): ?SystemCollectionManagerInterface
    {
        return $this->container->get('sulu_media.system_collections.manager');
    }

    /**
     * Get media manager.
     *
     * @return MediaManagerInterface|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getMediaManager(): ?MediaManagerInterface
    {
        return $this->container->get('sulu_media.media_manager');
    }

    public static function getSubscribedServices(): array
    {
        $subscribedServices = [];

        $subscribedServices['sulu_media.system_collections.manager'] = SystemCollectionManagerInterface::class;
        $subscribedServices['sulu_media.media_manager'] = MediaManagerInterface::class;

        return $subscribedServices;
    }

    /**
     * Get contact media collection.
     *
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getContactMediaCollection(): int
    {
        return $this->getSystemCollectionManager()->getSystemCollection('sulu_contact.contact');
    }
}
