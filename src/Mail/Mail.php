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


    /**
     * Mail constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->mail = new \PHPMailer(true);
        $this->mail->IsSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPKeepAlive = true;

        $options = $this->setConfig($options);

        $this->mail->SMTPSecure = $options['smtpsecure'];
        $this->mail->Port = $options['port'];
        $this->mail->Host = $options['host'];
        $this->mail->Username = $options['username'];
        $this->mail->Password = $options['password'];

    }

    /**
     * @param array $options
     * @return array
     */
    public function setConfig(array $options)
    {
        $default = [
            'smtpsecure' => '',
            // set the SMTP server port 25, 465 or 587
            'port'       => 25,
            // SMTP server
            'host'       => '',
            // SMTP server username
            'username'   => '',
            // SMTP server pass
            'password'   => ''
        ];

        return array_merge($default, $options);

    }

    /**
     * @return bool
     * @throws \Exception
     * @throws \phpmailerException
     */
    public function send()
    {

        if (empty($this->from)) {
            throw new \Exception('Response can not be empty');
        }

        if (empty($this->response)) {
            $this->response = $this->from;
        }

        $this->mail->AddReplyTo($this->response[0], $this->response[1]);

        $this->mail->From = $this->from[0];
        $this->mail->FromName = $this->from[1];

        $this->mail->AddAddress($this->to);

        if (!empty($this->tobcc)) {
            foreach ($this->tobcc as $m) {
                $this->mail->addBCC($m);
            }
        }

        $this->mail->Subject = $this->asunto;

        $body = $this->body;

        $this->mail->MsgHTML($body);

        $salida = $this->mail->Send();

        $this->ClearAddresses();

        return $salida;
    }

    /**
     * @param $asunto
     * @return $this
     */
    public function subject($asunto)
    {
        $this->asunto = $asunto;
        return $this;
    }

    /**
     * @param $body
     * @return $this
     */
    public function body($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param $email
     * @return $this
     */
    public function to($email)
    {
        $this->to = $email;
        return $this;
    }

    /**
     * @param array $emails_array
     * @return $this
     */
    public function tobcc(array $emails_array)
    {
        $this->tobcc = $emails_array;
        return $this;
    }

    /**
     * @param $email
     * @param string $nombre
     * @return $this
     */
    public function response($email, $nombre = '')
    {
        $this->response = array($email, $nombre);
        return $this;
    }

    /**
     * @param $email
     * @param $nombre
     * @return $this
     */
    public function from($email, $nombre)
    {
        $this->from = array($email, $nombre);
        return $this;
    }

    /**
     * @param $file
     * @param $nameattachment
     * @return $this
     * @throws \phpmailerException
     */
    public function AddAttachment($file, $nameattachment)
    {
        $this->mail->AddAttachment($file, $nameattachment);
        return $this;
    }

    /**
     * Remove addresses and attachments
     */
    public function ClearAddresses()
    {
        $this->mail->clearAddresses();
        $this->mail->clearAttachments();
    }

    /**
     * Close smpt connection
     */
    public function smtpClose()
    {
        $this->mail->smtpClose();
    }

}
