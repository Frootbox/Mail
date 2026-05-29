<?php
/**
 *
 */

namespace Frootbox\Mail\Transports;

class Localhost extends AbstractTransport
{
    protected $mailer = null;

    /**
     *
     */
    public function send(\Frootbox\Mail\Envelope $envelope, array $parameters = []): void
    {
        if ($this->mailer === null) {

            $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);

            $this->mailer->CharSet = "utf-8";
            $this->mailer->Encoding = 'base64';

            // Debug mode is optional
            if (!empty($this->config->get('mail.smtp.debug'))) {
                // $this->mailer->SMTPDebug  = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
                $this->mailer->SMTPDebug  = \PHPMailer\PHPMailer\SMTP::DEBUG_CONNECTION;
            }

            // Content
            $this->mailer->isHTML(true);
        }

        ini_set('sendmail_from', $this->getFromAddress());

        $this->mailer->clearAllRecipients();
        $this->mailer->clearAttachments();
        $this->mailer->setFrom($this->getFromAddress(), $this->getFromName(), false);
        $this->mailer->Subject = $envelope->getSubject();

        if (!empty($envelope->getReplyTo())) {
            $this->mailer->addReplyTo($envelope->getReplyTo()->getAddress(), $envelope->getReplyTo()->getName() ?? '');
        }
        else {
            $this->mailer->clearReplyTos();
        }

        $this->mailer->Body = $envelope->getBodyHtml();

        foreach ($envelope->getRecipients() as $recipient) {
            $this->mailer->addAddress($recipient->getAddress(), $recipient->getName());
        }

        foreach ($envelope->getBcc() as $recipient) {
            $this->mailer->addBcc($recipient->getAddress(), $recipient->getName());
        }

        foreach ($envelope->getAttachments() as $attachment) {
            $this->mailer->addAttachment($attachment->getPath(), $attachment->getName());
        }

        $this->mailer->send();
    }
}
