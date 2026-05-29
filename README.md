# Frootbox Mail

Small mail abstraction around [PHPMailer](https://github.com/PHPMailer/PHPMailer).

The package provides an `Envelope` object for message data and transport classes for SMTP or local `mail()`/sendmail delivery.

## Requirements

- PHP 8.0 or newer
- `ext-curl`
- `phpmailer/phpmailer`
- `frootbox/config`

## Installation

```bash
composer require frootbox/mail
```

## Configuration

Transports expect a `Frootbox\Config\Config` instance. A config file can return a PHP array:

```php
<?php

return [
    'mail' => [
        'defaults' => [
            'from' => [
                'address' => 'noreply@example.com',
                'name' => 'Example App',
            ],
        ],
        'smtp' => [
            'host' => 'smtp.example.com',
            'username' => 'smtp-user',
            'password' => 'smtp-password',
            'secure' => 'tls',
            'port' => 587,
            'debug' => false,
        ],
    ],
];
```

Load it like this:

```php
use Frootbox\Config\Config;

$config = new Config(__DIR__ . '/config.php');
```

## Sending Via SMTP

```php
use Frootbox\Config\Config;
use Frootbox\Mail\Attachment;
use Frootbox\Mail\Envelope;
use Frootbox\Mail\Transports\Smtp;

require __DIR__ . '/vendor/autoload.php';

$config = new Config(__DIR__ . '/config.php');

$envelope = new Envelope();
$envelope->setSubject('Welcome');
$envelope->setBodyHtml('<h1>Hello</h1><p>Thanks for signing up.</p>');
$envelope->addTo('person@example.com', 'Jane Person');
$envelope->addBcc('archive@example.com');
$envelope->setReplyTo('support@example.com', 'Support Team');
$envelope->addAttachment(new Attachment(__DIR__ . '/terms.pdf', 'terms.pdf'));

$transport = new Smtp($config);
$transport->send($envelope);
```

You can override SMTP settings for one transport instance:

```php
$transport = new Smtp($config);
$transport->setOverrideSettings([
    'host' => 'smtp.other-provider.com',
    'username' => 'other-user',
    'password' => 'other-password',
    'secure' => 'tls',
    'port' => 587,
]);

$transport->send($envelope);
```

## Sending Via Localhost

Use `Localhost` when the server is configured to send mail via PHP's local mail transport:

```php
use Frootbox\Config\Config;
use Frootbox\Mail\Envelope;
use Frootbox\Mail\Transports\Localhost;

require __DIR__ . '/vendor/autoload.php';

$config = new Config(__DIR__ . '/config.php');

$envelope = new Envelope();
$envelope->setSubject('Local test');
$envelope->setBodyHtml('<p>Hello from localhost.</p>');
$envelope->addTo('person@example.com');

$transport = new Localhost($config);
$transport->send($envelope);
```

## Custom From Address

Both transports use `mail.defaults.from.*` from the config by default. Override it per transport instance with `setFrom()`:

```php
$transport->setFrom('billing@example.com', 'Billing Team');
$transport->send($envelope);
```

## Inline Images

The SMTP transport can fetch image URLs from the HTML body and embed them as CID attachments:

```php
$envelope->setBodyHtml('<p><img src="https://example.com/logo.png" alt="Logo"></p>');

$transport->send($envelope, [
    'inlineImages' => true,
]);
```

Images whose `src` already starts with `cid:` are skipped.

## Envelope API

Common methods:

- `setSubject(string $subject): void`
- `setBodyHtml(string $html): void`
- `addTo(string $address, ?string $name = null): void`
- `addBcc(string $address, ?string $name = null): void`
- `setReplyTo(?string $address = null, ?string $name = null): void`
- `addAttachment(Attachment $attachment): void`
- `clearTo(): void`
- `clearBcc(): void`
- `clearReplyTo(): void`

`Attachment` accepts a filesystem path and an optional display name:

```php
$envelope->addAttachment(new Attachment('/path/to/report.pdf'));
$envelope->addAttachment(new Attachment('/path/to/report.pdf', 'monthly-report.pdf'));
```

## Custom Transports

Custom transports should implement `Frootbox\Mail\Transports\Interfaces\TransportInterface`:

```php
use Frootbox\Mail\Envelope;
use Frootbox\Mail\Transports\Interfaces\TransportInterface;

final class LogTransport implements TransportInterface
{
    public function send(Envelope $envelope, array $parameters = []): void
    {
        // Deliver or record the envelope.
    }

    public function setFrom(string $address, ?string $name = null): void
    {
        // Store sender data for later use.
    }
}
```

## License

GPL-3.0-only. See [LICENSE](LICENSE).
