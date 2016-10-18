<?php

namespace Buuum;

use Buuum\MailerInterface;
use Buuum\MailerHandler\SwiftMailerHandler;
use Buuum\MailerHandler\PHPMailerHandler;

class Mail
{

    /**
     * @var string
     */
    private $to = '';
    /**
     * @var array
     */
    private $tobcc = [];
    /**
     * @var string
     */
    private $asunto = '';
    /**
     * @var string
     */
    private $body = '';
    /**
     * @var array
     */
    private $from = [];
    /**
     * @var array
     */
    private $response = [];
    /**
     * @var MailerInterface
     */
    private $mailer;

    private static $instance;

    public function __construct ( MailerInterface $mailer ) {
        $this->mailer = $mailer;
    }

    public static function setConfig(array $options)
    {
        $required = [
            'host', 'username', 'port', 'password',
            'smtpsecure', 'from', 'response',
            'mailer'
        ];
        if(count(array_intersect_key(array_flip($required), $options)) < count($required)) {
            throw new \InvalidArgumentException( 'Insuficient transport config params.' );
        }
        $myself = self::getInstance($options['mailer']);
        $myself->mailer->setConfig( $options );
        $myself->response = $options['response'];
        $myself->from = $options['from'];
    }
    /**
     * @return bool
     * @param $directoryPath
     * @throws \Exception
     * @throws \siwftMailerException
     */
    public function spool ( $directoryPath ) {
        $myself = self::getInstance();
        return $myself->mailer->spool( $myself->asunto, $myself->from,
                                      $myself->response, $myself->to,
                                      $myself->body, $directoryPath );
    }

    /**
     * @return bool
     * @throws \Exception
     * @throws \phpmailerException
     */
    public static function send()
    {
        $myself = self::getInstance();
        if (empty($myself->from)) {
            throw new \Exception('From field can not be empty');
        }
        if (empty($myself->response)) {
            $myself->response = $myself->from;
        }
        if (!empty($myself->tobcc)) {
            $myself->mailer->setBcc( $myself->tobcc );
        }
        $replyTo = false;
        if (!empty($myself->response) ) {
            $replyTo = $myself->response;
        }
        return $myself->mailer->send($myself->asunto, $myself->from,
                                        $myself->response, $myself->to,
                                        $myself->body );
    }

    public static function subject($asunto)
    {
        $myself = self::getInstance();
        $myself->asunto = $asunto;
        return $myself;
    }

    /**
     * @param $body
     * @return $this
     */
    public static function body($body)
    {
        $myself = self::getInstance();
        $myself->body = $body;
        return $myself;
    }

    /**
     * @param $email
     * @return $this
     */
    public static function to($email)
    {
        $myself = self::getInstance();
        $myself->to = $email;
        return $myself;
    }

    /**
     * @param array $emails_array
     * @return $this
     */
    public static function tobcc(array $emails_array)
    {
        $myself = self::getInstance();
        $myself->tobcc = $emails_array;
        return $myself;
    }

    /**
     * @param $email
     * @param string $nombre
     * @return $this
     */
    public static function response($email, $nombre = '')
    {
        $myself = self::getInstance();
        $myself->response = [$email, $nombre];
        return $myself;
    }

    /**
     * @param $email
     * @param $nombre
     * @return $this
     */
    public static function from($email, $nombre)
    {
        $myself = self::getInstance();
        $myself->from = [$email, $nombre];
        return $myself;
    }

    /**
     * @param $filePathOrUrl
     * @param $attachmentName
     * @return $this
     * @throws \phpmailerException
     */
    public static function addAttachment($filePathOrUrl, $attachmentName)
    {
        $myself = self::getInstance();
        $myself->mailer->addAttachment($filePathOrUrl, $attachmentName);
        return $myself;
    }

    /**
     * @param $mailerName this is the library that you will use, it can be SwiftMailer or PhpMailer
     * @return SwiftMailerHandler | PHPMailerHandler
     */
    public static function getMailerInstance ( $mailerName=false )
    {
        if (!isset(self::$mailer)) {
            if ( $mailerName && strtolower($mailerName) == 'swift' ){
                return new SwiftMailerHandler;
            }
            return new PHPMailerHandler;
        }
        return self::$mailer;
    }

    /**
     * @param $mailerName the library to use can be SwiftMailer or PhpMailer
     * @return $this
     */
    public static function getInstance($mailerName=false)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self( self::getMailerInstance( $mailerName ) );
        }
        return self::$instance;
    }

}
