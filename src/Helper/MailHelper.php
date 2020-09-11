<?php

namespace App\Helper;

use Symfony\Component\{
    Mime\Email,
    Mailer\Mailer,
    Mailer\MailerInterface
};
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Twig\{
    Environment,
    Error\LoaderError,
    Error\RuntimeError,
    Error\SyntaxError
};


class MailHelper
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $templating;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $noReplySender;

    /**
     * MailHelper constructor.
     *
     * @param Environment $templating
     * @param ParameterBagInterface $params
     * @param $prefix
     * @param $sender
     * @param MailerInterface $mailer
     */
    public function __construct(
        Environment $templating,
        ParameterBagInterface $params,
        $prefix,
        $sender,
        MailerInterface $mailer
    ) {
        $this->mailer        = $mailer;
        $this->templating    = $templating;
        $this->params        = $params;
        $this->prefix        = $prefix;
        $this->noReplySender = $sender;
    }

    /**
     * @param string $emailFrom
     * @param array|string $emailTo
     * @param string $emailSubject
     * @param array $emailContent
     * @param string $emailTemplate
     * @param array $attachedFiles
     * @param array $emailCci
     * @param array $emailCc
     * @param bool $noReply
     * @param bool $cron
     *
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function sendEmail(
        string $emailFrom,
        $emailTo,
        string $emailSubject,
        array $emailContent,
        string $emailTemplate,
        array $attachedFiles = [],
        array $emailCc = [],
        array $emailCci = [],
        bool $noReply = true,
        bool $cron = false
    ): bool {
        /**
         * Return false if function used in bash command
         */
        if (php_sapi_name() === 'cli' && $cron === false) {
            return false;
        }

        try {
            $message = new Email();

            $subject = $this->prefix . ': ' . $emailSubject;
            $message->subject($subject);
            $message->html(
                $this->makeBody($emailTemplate, $message, $emailContent)
            );

            if (is_array($emailTo) === false) {
                $emailTo = [$emailTo];
            }

            foreach ($emailTo as $email) {
                $message->addTo($email);
            }

            if ($noReply === true) {
                $message->from($this->noReplySender);
            } else {
                $message->from($emailFrom);
            }

            if (false === empty($emailCc)) {
                foreach ($emailCc as $email) {
                    $message->addCc($email);
                }
            }

            if (false === empty($emailCci)) {
                foreach ($emailCci as $email) {
                    $message->addBcc($email);
                }
            }

            if (false === empty($attachedFiles)) {
                foreach ($attachedFiles as $attachedFile) {
                    $message->attachFromPath($attachedFile['file'], $attachedFile['name']);
                }
            }
            $this->mailer->send($message);
            return true;
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            return false;
        }
    }

    /**
     * @param string $emailTemplate
     * @param $message
     * @param array $content
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function makeBody(string $emailTemplate, $message, $content = [])
    {
        $content['env_url'] = $_SERVER['FRONT_SITE_DOMAIN'];
        $body               = $this->templating->render($emailTemplate, $content);
        $body               = html_entity_decode($body);

        return $body;
    }
}