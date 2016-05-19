<?php

namespace Buuum;

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


    private static $instance;

    public function __construct()
    {
        $this->mail = new \PHPMailer(true);
        $this->mail->IsSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPKeepAlive = true;

    }

    public static function setConfig(array $options)
    {
        $myself = self::getInstance();

        $default = [
            'smtpsecure' => '',
            // set the SMTP server port 25, 465 or 587
            'port'       => 25,
            // SMTP server
            'host'       => '',
            // SMTP server username
            'username'   => '',
            // SMTP server pass
            'password'   => '',
            // FROM
            'from'       => [],
            // RESPONSE
            'response'   => []
        ];

        $options = array_merge($default, $options);

        $myself->mail->SMTPSecure = $options['smtpsecure'];
        $myself->mail->Port = $options['port'];
        $myself->mail->Host = $options['host'];
        $myself->mail->Username = $options['username'];
        $myself->mail->Password = $options['password'];
        $myself->from = $options['from'];
        $myself->response = $options['response'];

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
            throw new \Exception('Response can not be empty');
        }

        if (empty($myself->response)) {
            $myself->response = $myself->from;
        }

        $myself->mail->AddReplyTo($myself->response[0], $myself->response[1]);

        $myself->mail->From = $myself->from[0];
        $myself->mail->FromName = $myself->from[1];

        $myself->mail->AddAddress($myself->to);

        if (!empty($myself->tobcc)) {
            foreach ($myself->tobcc as $m) {
                $myself->mail->addBCC($m);
            }
        }

        $myself->mail->Subject = $myself->asunto;

        $body = $myself->body;

        $myself->mail->MsgHTML($body);

        $salida = $myself->mail->Send();

        $myself->ClearAddresses();

        return $salida;
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
     * @param $file
     * @param $nameattachment
     * @return $this
     * @throws \phpmailerException
     */
    public static function AddAttachment($file, $nameattachment)
    {
        $myself = self::getInstance();
        $myself->mail->AddAttachment($file, $nameattachment);
        return $myself;
    }

    /**
     * Remove addresses and attachments
     */
    public static function ClearAddresses()
    {
        $myself = self::getInstance();
        $myself->mail->clearAddresses();
        $myself->mail->clearAttachments();
    }

    /**
     * Close smpt connection
     */
    public function smtpClose()
    {
        $this->mail->smtpClose();
    }

    public static function getInstance()
    {

        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

}
