<?php
/**
 *
 */

namespace Frootbox\Mail;

class Envelope
{
    protected ?string $subject = null;
    protected array $to = [];
    protected array $bcc = [];
    protected ?\Frootbox\Mail\Recipient $replyTo = null;
    protected ?string $bodyHtml = null;
    protected array $attachments = [];

    /**
     *
     */
    public function addAttachment(\Frootbox\Mail\Attachment $attachment): void
    {
        $this->attachments[] = $attachment;
    }

    /**
     *
     */
    public function addTo(string $address, string $name = null): void
    {
        $this->to[] = new Recipient($address, $name);
    }

    /**
     *
     */
    public function addBcc(string $address, string $name = null): void
    {
        $this->bcc[] = new Recipient($address, $name);
    }

    /**
     *
     */
    public function clearBcc(): void
    {
        $this->bcc = [];
    }

    /**
     * Clear reply to
     *
     * @return void
     */
    public function clearReplyTo(): void
    {
        $this->replyTo = null;
    }

    /**
     *
     */
    public function clearTo(): void
    {
        $this->to = [];
    }

    /**
     *
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     *
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     *
     */
    public function getBodyHtml(): ?string
    {
        return $this->bodyHtml;
    }

    /**
     *
     */
    public function getRecipients(): array
    {
        return $this->to;
    }

    /**
     *
     */
    public function getReplyTo(): ?\Frootbox\Mail\Recipient
    {
        return $this->replyTo;
    }

    /**
     *
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     *
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     *
     */
    public function setBodyHtml(string $html): void
    {
        $this->bodyHtml = $html;
    }

    /**
     *
     */
    public function setReplyTo(string $address = null, string $name = null): void
    {
        if ($address === null) {
            $this->replyTo = null;
        }
        else {
            $this->replyTo = new Recipient($address, $name);
        }
    }

    /**
     *
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * Replace array of recipients
     *
     * @param array $to
     * @return void
     */
    public function setTo(array $to): void
    {
        $this->to = $to;
    }
}
