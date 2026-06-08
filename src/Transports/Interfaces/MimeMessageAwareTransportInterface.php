<?php
/**
 *
 */

namespace Frootbox\Mail\Transports\Interfaces;

interface MimeMessageAwareTransportInterface
{
    /**
     * @return string|null
     */
    public function getLastMimeMessage(): ?string;
}
