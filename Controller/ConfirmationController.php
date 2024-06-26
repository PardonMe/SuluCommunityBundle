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
use Sulu\Bundle\CommunityBundle\DependencyInjection\Configuration;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use function str_replace;

/**
 * Handles the confirmation page.
 */
class ConfirmationController extends AbstractController
{
    public const TYPE = Configuration::TYPE_CONFIRMATION;

    /**
     * Confirm user email address by token.
     *
     * @param Request $request
     * @param string $token
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function indexAction(Request $request, string $token): Response
    {
        $communityManager = $this->getCommunityManager($this->getWebspaceKey());

        $success = false;

        // Confirm user by token
        $user = $communityManager->confirm($token);

        if ($user instanceof User) {
            // Save User
            $this->saveEntities();

            // Login
            if ($this->checkAutoLogin(Configuration::TYPE_CONFIRMATION)) {
                $communityManager->login($user, $request);
            }

            // Redirect
            $redirectTo = $communityManager->getConfigTypeProperty(self::TYPE, Configuration::REDIRECT_TO);

            if ($redirectTo) {
                if (str_starts_with($redirectTo, '/')) {
                    $url = str_replace('{localization}', $request->getLocale(), $redirectTo);
                } else {
                    $url = $this->getRouter()->generate($redirectTo);
                }

                return $this->redirect($url);
            }

            $success = true;
        }

        return $this->renderTemplate(Configuration::TYPE_CONFIRMATION, ['success' => $success]);
    }

    /**
     * @return RouterInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getRouter(): RouterInterface
    {
        return $this->container->get('router');
    }

    /**
     * @return array<string|int, string>
     */
    public static function getSubscribedServices(): array
    {
        $subscribedServices = parent::getSubscribedServices();

        $subscribedServices['router'] = RouterInterface::class;

        return $subscribedServices;
    }
}
