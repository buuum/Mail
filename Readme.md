Mail - Simple Mailer Class with phpmailer
=========================================

## Install

### System Requirements

You need PHP >= 5.5.0 to use Buuum\Mail but the latest stable version of PHP is recommended.

### Composer

Buuum is available on Packagist and can be installed using Composer:

```
composer require buuum/mail
```

### Manually

You may use your own autoloader as long as it follows PSR-0 or PSR-4 standards. Just put src directory contents in your vendor directory.

## Initialize

```php

use Buuum\Mail;
use Buuum\MailerHandler\SwiftMailerHandler;
use Buuum\MailerHandler\PhpMailerHandler;

$config = [
    'smtpsecure'      => 'tls',
    'host'            => "smtp.host.com",
    'username'        => "host_username",
    'password'        => "host_password",
    'port'            => 25,
    'from'            => ['from@host.com', 'WebName'],
    'response'        => ['response@host.com', 'WebName'],
    'spool'           => true,
    'spool_directory' => __DIR__ . '/spool'
];

// SWIFTHANDLER
$handler = new SwiftMailerHandler($config);
// PHPMAILER HANDLER
$handler = new PhpMailerHandler($config);

$mail = Mail::getInstance();
$mail->setHandler($handler);

```

##  Send Mails
```php

$mail->subject('subject')->to('email@to.com')->body('message body');
$mail->AddAttachment(__DIR__ . '/i.jpg', 'imagen.jpg');
$mail->AddAttachment("http://blog.caranddriver.com/wp-content/uploads/2015/11/BMW-2-series.jpg", 'car.jpg');
$mail->send();

```

## Send Mails with swiftmailer spool
This code save mails on queue.
```php

$mail->subject('subject')->to('email@to.com')->body('message body');
$mail->AddAttachment(__DIR__ . '/i.jpg', 'imagen.jpg');
$mail->AddAttachment("http://blog.caranddriver.com/wp-content/uploads/2015/11/BMW-2-series.jpg", 'car.jpg');
$mail->send(false);

```

## Send mails queue
```php

$handler->sendSpooledMessages($messageLimit = 100, $timeLimit = 60);

```

## Multiple sends

```php
$mails = [
    'email1@example.com',
    'email2@example.com',
    'email3@example.com'
];

$mail->subject('subject')->body('message body');

foreach($mails as $mail){
    $mail->to($mail)->send();
}
```