<?php

namespace Buuum\MailerHandler;

use Buuum\MailHandlerInterface;

class PhpMailerHandler implements MailHandlerInterface
{

    /**
     * @var \PHPMailer
     */
    protected $mail;

    /**
     * PhpMailerHandler constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->mail = new \PHPMailer(true);
        $this->mail->IsSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPKeepAlive = true;

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

        $this->mail->SMTPSecure = $options['smtpsecure'];
        $this->mail->Port = $options['port'];
        $this->mail->Host = $options['host'];
        $this->mail->Username = $options['username'];
        $this->mail->Password = $options['password'];

        $this->mail->From = $options['from'][0];
        $this->mail->FromName = $options['from'][0];

        $this->mail->AddReplyTo($options['response'][0], $options['response'][1]);
    }

    /**
     * @param bool $immediately
     * @return bool
     * @throws \phpmailerException
     */
    public function send($immediately = true)
    {

        $salida = $this->mail->Send();

        $this->mail->clearAddresses();
        $this->mail->clearAttachments();

        return $salida;
    }

    /**
     * @param $asunto
     * @return $this
     */
    public function subject($asunto)
    {
        $this->mail->Subject = $asunto;
        return $this;
    }

    /**
     * @param $body
     * @return $this
     */
    public function body($body)
    {
        $this->mail->MsgHTML($body);
        return $this;
    }

    /**
     * @param $email
     * @return $this
     */
    public function to($email)
    {
        $this->mail->AddAddress($email);
        return $this;
    }

    /**
     * @param array $emails_array
     * @return $this
     */
    public function tobcc(array $emails_array)
    {
        if (!empty($emails_array)) {
            foreach ($emails_array as $m) {
                $this->mail->addBCC($m);
            }
        }else{
            $this->mail->addBCC([]);
        }
        return $this;
    }

    /**
     * @param $email
     * @param string $nombre
     * @return $this
     */
    public function response($email, $nombre = '')
    {
        $this->mail->AddReplyTo($email, $nombre);
        return $this;
    }

    /**
     * @param $email
     * @param $nombre
     * @return $this
     */
    public function from($email, $nombre)
    {
        $this->mail->From = $email;
        $this->mail->FromName = $nombre;
        return $this;
    }

    /**
     *
     */
    public function close()
    {
        $this->mail->smtpClose();
    }

    /**
     * @param $file
     * @param $nameattachment
     * @return $this
     * @throws \phpmailerException
     */
    public function AddAttachment($file, $nameattachment)
    {
        if ($this->isUrl($file)) {
            $this->mail->addStringAttachment(file_get_contents($file), $nameattachment);
        } else {
            $this->mail->AddAttachment($file, $nameattachment);
        }
        return $this;
    }

    /**
     * @param $possibleUrl
     * @return bool
     */
    private function isUrl($possibleUrl)
    {
        return filter_var($possibleUrl, FILTER_VALIDATE_URL) == true;
    }

}