<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CommunityBundle\Mail;

use Sulu\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use function array_merge;

/**
 * Send emails for a specific type.
 */
class MailFactory implements MailFactoryInterface
{
    protected Mailer $mailer;

    protected Environment $twig;

    protected TranslatorInterface $translator;

    public function __construct(Mailer $mailer, Environment $twig, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->translator = $translator;
    }

    /**
     * @param Mail $mail
     * @param User $user
     * @param array $parameters
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    public function sendEmails(Mail $mail, User $user, array $parameters = []): void
    {
        $email = $mail->getUserEmail();
        if (!$email) {
            $email = $user->getEmail();
        }
        $data = array_merge($parameters, ['user' => $user]);

        // Send User Email
        if (null !== $mail->getUserTemplate() && $email) {
            /** @var LocaleAwareInterface $translator */
            $translator = $this->translator;
            // Render Email in specific locale
            $locale = $translator->getLocale();
            $translator->setLocale($user->getLocale());

            $this->sendEmail($mail->getFrom(), $email, $mail->getSubject(), $mail->getUserTemplate(), $data);
            $translator->setLocale($locale);
        }

        // Send Admin Email
        if (null !== $mail->getAdminTemplate()) {
            $this->sendEmail($mail->getFrom(), $mail->getTo(), $mail->getSubject(), $mail->getAdminTemplate(), $data);
        }
    }

    /**
     * Create and send email
     *
     * @param $from
     * @param $to
     * @param string $subject
     * @param string $template
     * @param array $data
     * @return void
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function sendEmail($from, $to, string $subject, string $template, array $data): void
    {
        $body = $this->twig->render($template, $data);

        $message = new Email();
        $message->subject($this->translator->trans($subject));

        if (is_array($from)) {
            foreach ($from as $_from) {
                $message->from($_from);
            }
        } else {
            $message->from((string) $from);
        }

        if (is_array($to)) {
            foreach ($to as $_to) {
                $message->to($_to);
            }
        } else {
            $message->to((string) $to);
        }

        $message->html($body);

        $this->mailer->send($message);
    }
}
