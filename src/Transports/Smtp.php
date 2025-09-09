<?php
/**
 *
 */

namespace Frootbox\Mail\Transports;

class Smtp extends AbstractTransport
{
    protected $mailer = null;
    protected $overrideSettings = [];

    /**
     * @param array $settings
     * @return void
     */
    public function setOverrideSettings(array $settings): void
    {
        $this->overrideSettings = $settings;
    }

    /**
     * @param \Frootbox\Mail\Envelope $envelope
     * @param array $parameters
     * @return void
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send(\Frootbox\Mail\Envelope $envelope, array $parameters = []): void
    {
        if (!empty($this->overrideSettings)) {

            $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);

            //Server settings
            $this->mailer->isSMTP();
            $this->mailer->SMTPKeepAlive = true;
            $this->mailer->Host = $this->overrideSettings['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->overrideSettings['username'];
            $this->mailer->Password = $this->overrideSettings['password'];
            $this->mailer->SMTPSecure = $this->overrideSettings['secure'] ?? 'tls';
            $this->mailer->Port = $this->overrideSettings['port'] ?? 587;
            $this->mailer->CharSet = "utf-8";
            $this->mailer->Encoding = 'base64';
            $this->mailer->SMTPOptions = array (
                'ssl' => array (
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ),
            );
            $this->mailer->XMailer = null;

            // Debug mode is optional
            if (!empty($this->config->get('mail.smtp.debug'))) {
                // $this->mailer->SMTPDebug  = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
                $this->mailer->SMTPDebug  = \PHPMailer\PHPMailer\SMTP::DEBUG_CONNECTION;
            }

            // Content
            $this->mailer->isHTML(true);
        }
        elseif ($this->mailer === null) {

            $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);

            //Server settings
            $this->mailer->isSMTP();
            $this->mailer->SMTPKeepAlive = true;
            $this->mailer->Host = $this->config->get('mail.smtp.host');
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config->get('mail.smtp.username');
            $this->mailer->Password = $this->config->get('mail.smtp.password');
            $this->mailer->SMTPSecure = ($this->config->get('mail.smtp.secure') ?? 'tls');
            $this->mailer->Port = ($this->config->get('mail.smtp.port') ?? 587); ;
            $this->mailer->CharSet = "utf-8";
            $this->mailer->Encoding = 'base64';
            $this->mailer->SMTPOptions = array (
                'ssl' => array (
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ),
            );
            $this->mailer->XMailer = null;

            // Debug mode is optional
            if (!empty($this->config->get('mail.smtp.debug'))) {
                // $this->mailer->SMTPDebug  = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
                $this->mailer->SMTPDebug  = \PHPMailer\PHPMailer\SMTP::DEBUG_CONNECTION;
            }

            // Content
            $this->mailer->isHTML(true);
        }

        // Clear attachments
        $this->mailer->clearAttachments();

        // Set from
        $this->mailer->setFrom($this->getFromAddress(), $this->getFromName(), false);

        // Set subject
        $this->mailer->Subject = $envelope->getSubject();

        if (!empty($parameters['inlineImages'])) {

            $html = $envelope->getBodyHtml();

            preg_match_all('#<img(.*?)src="(.*?)"(.*?)>#', $html, $matches);

            $loop = 1;

            foreach ($matches[0] as $index => $tagline) {

                // Skip already inlined images
                if (str_starts_with($matches[2][$index], 'cid:')) {
                    continue;
                }

                // Fetch source of image
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $matches[2][$index]);
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $source = curl_exec ($ch);
                curl_close ($ch);

                // Create temporary file for image
                $tempFile = tempnam(sys_get_temp_dir(), '_mailinline_image');

                $handle = fopen($tempFile, "w");
                fwrite($handle, $source);
                fclose($handle);

                // Add image file to inliner
                $this->mailer->addEmbeddedImage($tempFile, 'image-' . $loop, basename($matches[2][$index]));

                $newTagline = str_replace($matches[2][$index], 'cid:image-' . $loop, $tagline);

                $html = str_replace($tagline, $newTagline, $html);

                ++$loop;
            }

            $envelope->setBodyHtml($html);
        }

        if (!empty($envelope->getReplyTo())) {
            $this->mailer->addReplyTo($envelope->getReplyTo()->getAddress());
        }
        else {
            $this->mailer->clearReplyTos();
        }

        $this->mailer->Body = $envelope->getBodyHtml();

        // Clear recipients
        $this->mailer->clearAllRecipients();

        $def = error_reporting();
        error_reporting(E_ERROR | E_WARNING | E_PARSE);

        foreach ($envelope->getRecipients() as $recipient) {
            $this->mailer->addAddress($recipient->getAddress(), $recipient->getName());
        }

        foreach ($envelope->getBcc() as $recipient) {
            $this->mailer->addBcc($recipient->getAddress(), $recipient->getName());
        }

        error_reporting($def);

        foreach ($envelope->getAttachments() as $attachment) {
            $this->mailer->addAttachment($attachment->getPath(), $attachment->getName());
        }

        $this->mailer->send();
    }
}
