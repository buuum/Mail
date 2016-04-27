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

```
$mail = new \Buuum\Mail([
    'host'       => "myhost.com",
    'username'   => "myuser@host.com",
    'password'   => "mypass"
]);
```

##  Send Mails
```
$mail->from('emailsender@myhost.com', 'My Host')
    ->response('responsemail@myhost.com', 'My Host')
    ->to('emailreciver@gmail.com')
    ->asunto('Subject mail')
    ->body('<h1>Mail message</h1>')
    ->tobcc(['email1@example.com', 'email2@example.com'])
    ->AddAttachment(__DIR__.'/i.jpg', 'imagen.jpg')
    ->send();
```

## Multiple sends

```
$mails = [
    'email1@example.com',
    'email2@example.com',
    'email3@example.com'
];

$mail->subject('subject mail')
->body('message body');

foreach($mails as $mail){
    $mail->to($mail)->send();
}
```