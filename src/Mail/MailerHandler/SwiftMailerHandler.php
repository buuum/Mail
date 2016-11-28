<?php

namespace Buuum\MailerHandler;

use Buuum\MailHandlerInterface;

class SwiftMailerHandler implements MailHandlerInterface
{

    /**
     * @var \Swift_Mailer
     */
    protected $mail;

    /**
     * @var \Swift_Message
     */
    protected $message;

    /**
     * @var \Swift_Transport
     */
    protected $transport;

    /**
     * @var \Swift_SpoolTransport
     */
    protected $transport_spool = false;

    /**
     * SwiftMailerHandler constructor.
     * @param $options
     */
    public function __construct($options)
    {

        $default = [
            'smtpsecure'      => '',
            // set the SMTP server port 25, 465 or 587
            'port'            => 25,
            // SMTP server
            'host'            => '',
            // SMTP server username
            'username'        => '',
            // SMTP server pass
            'password'        => '',
            // FROM
            'from'            => [],
            // RESPONSE
            'response'        => [],
            'spool'           => false,
            'spool_directory' => ''
        ];

        $options = array_merge($default, $options);

        $this->transport = \Swift_SmtpTransport::newInstance($options['host'], $options['port'], $options['smtpsecure'])
            ->setUsername($options['username'])
            ->setPassword($options['password']);

        if ($options['spool']) {
            $spool = new \Swift_FileSpool($options['spool_directory']);
            $this->transport_spool = \Swift_SpoolTransport::newInstance($spool);
        }

        $this->message = \Swift_Message::newInstance();
        $this->message->setFrom([$options['from'][0] => $options['from'][1]]);
        $this->message->setReplyTo([$options['response'][0] => $options['response'][1]]);
    }

    /**
     * @param bool $immediately
     * @return int
     */
    public function send($immediately = true)
    {
        $transport = ($immediately) ? $this->transport : $this->transport_spool;
        $this->mail = \Swift_Mailer::newInstance($transport);
        return $this->mail->send($this->message);
    }

    /**
     * @param $subject
     */
    public function subject($subject)
    {
        $this->message->setSubject($subject);
    }

    /**
     * @param $body
     * @return $this
     */
    public function body($body)
    {
        $this->message->setBody($body);
        return $this;
    }

    /**
     * @param $email
     * @return $this
     */
    public function to($email)
    {
        $this->message->setTo([$email]);
        return $this;
    }

    /**
     * @param array $emails_array
     */
    public function tobcc(array $emails_array)
    {
        $this->message->setBcc($emails_array);
    }

    /**
     * @param $email
     * @param string $nombre
     */
    public function response($email, $nombre = '')
    {
        $this->message->setReplyTo([$email => $nombre]);
    }

    /**
     * @param $email
     * @param $nombre
     */
    public function from($email, $nombre)
    {
        $this->message->setFrom([$email => $nombre]);
    }

    /**
     * @param $file
     * @param $nameattachment
     * @return $this
     */
    public function AddAttachment($file, $nameattachment)
    {
        $this->message->attach(\Swift_Attachment::fromPath($file)->setFilename($nameattachment));
        return $this;
    }

    /**
     * @param int $messageLimit
     * @param int $timeLimit
     * @return int
     */
    public function sendSpooledMessages($messageLimit = 100, $timeLimit = 60)
    {
        $spool = $this->transport_spool->getSpool();
        $spool->setMessageLimit($messageLimit);
        $spool->setTimeLimit($timeLimit);
        return $spool->flushQueue($this->transport);
    }

    /**
     *
     */
    public function close()
    {
        $this->mail->getTransport()->stop();
    }
}