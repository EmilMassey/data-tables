<?php

namespace App\MailNotification;

use App\Event\UserHasBeenGrantedAccessToTableEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NotifyUserOnTableAccess
{
    /**
     * @var string
     */
    private $emailFrom;
    /**
     * @var string
     */
    private $siteUrl;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        string $emailFrom,
        string $siteUrl,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ) {
        $this->emailFrom = $emailFrom;
        $this->siteUrl = $siteUrl;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    public function __invoke(UserHasBeenGrantedAccessToTableEvent $event): void
    {
        $message = (new TemplatedEmail())
            ->from(new Address($this->emailFrom))
            ->to(new Address($event->user()->getEmail()))
            ->subject($this->translator->trans('mail_notification.access_to_table_granted.subject'))
            ->htmlTemplate('email/access_to_table_granted.html.twig')
            ->context([
                'user' => $event->user(),
                'table' => $event->table(),
                'siteUrl' => $this->siteUrl,
            ]);

        $this->mailer->send($message);
    }
}
