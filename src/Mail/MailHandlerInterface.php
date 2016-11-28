<?php

namespace Buuum;

interface MailHandlerInterface
{

    public function send($immediately = true);
    public function subject($subject);
    public function body($body);
    public function to($email);
    public function tobcc(array $emails_array);
    public function response($email, $nombre = '');
    public function from($email, $nombre);
    public function AddAttachment($file, $nameattachment);

    public function close();

}