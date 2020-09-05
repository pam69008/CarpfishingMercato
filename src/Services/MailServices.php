<?php

namespace App\Services;

use App\Helper\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class MailServices
{
    /** @var MailHelper  */
    protected $mailer;

    /** @var EntityManagerInterface  */
    private $em;

    /**
     * @var string
     */
    private $sender;

    /**
     * @var string
     */
    private $contactEmail;

    /**
     * MailServices constructor.
     * @param MailHelper $mailer
     * @param EntityManagerInterface $em
     * @param string $sender
     * @param string $contactEmail
     */
    public function __construct( MailHelper $mailer, EntityManagerInterface $em, string $sender, string $contactEmail)
    {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->sender = $sender;
        $this->contactEmail = $contactEmail;
    }

    public function userCreateConfirmation (string $emailTo, $token) : bool
    {
        $emailFrom = '';
        $emailSubject = 'Nouveau Compte';
        $emailContent = ['token'=> $token];
        $emailTemplate = 'emails/activation.html.twig';
            return $this->mailer->sendEmail($emailFrom, $emailTo, $emailSubject, $emailContent, $emailTemplate);
    }

}