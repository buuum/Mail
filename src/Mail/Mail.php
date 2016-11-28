<?php

namespace Buuum;

class Mail
{

    /**
     * @var Mail
     */
    private static $instance;
    /**
     * @var MailHandlerInterface
     */
    protected $handler;

    /**
     * @param $handler
     */
    public function setHandler(MailHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param bool $immediately
     */
    public function send($immediately = true)
    {
        $this->handler->send($immediately);
    }

    /**
     * @param $asunto
     * @return $this
     */
    public function subject($asunto)
    {
        $this->handler->subject($asunto);
        return $this;
    }

    /**
     * @param $body
     * @return $this
     */
    public function body($body)
    {
        $this->handler->body($body);
        return $this;
    }

    /**
     * @param $email
     * @return $this
     */
    public function to($email)
    {
        $this->handler->to($email);
        return $this;
    }

    /**
     * @param array $emails_array
     * @return $this
     */
    public function tobcc(array $emails_array)
    {
        $this->handler->tobcc($emails_array);
        return $this;
    }

    /**
     * @param $email
     * @param string $nombre
     * @return $this
     */
    public function response($email, $nombre = '')
    {
        $this->handler->response($email, $nombre);
        return $this;
    }

    /**
     * @param $email
     * @param $nombre
     * @return $this
     */
    public function from($email, $nombre)
    {
        $this->handler->from($email, $nombre);
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
        $this->handler->AddAttachment($file, $nameattachment);
        return $this;
    }

    /**
     * Close smpt connection
     */
    public function close()
    {
        $this->handler->close();
    }

    /**
     * @return Mail
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}
