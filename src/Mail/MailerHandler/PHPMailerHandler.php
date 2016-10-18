<?php

namespace Buuum\MailerHandler;

use Buuum\MailerInterface;

class PHPMailerHandler implements MailerInterface {
    private $mailerObj;

    public function __construct ( ) {
        $this->setMailer();
    }

    public function setMailer ( ) {
        $this->mailerObj = new \PHPMailer(true);
        return $this;
    }

    public function setConfig ( array $params ) {
        $this->mailerObj->SMTPSecure = $params['smtpsecure'];
        $this->mailerObj->IsSMTP();
        $this->mailerObj->SMTPAuth = true;
        $this->mailerObj->SMTPKeepAlive = true;
        $this->mailerObj->Port = $params['port'];
        $this->mailerObj->Host = $params['host'];
        $this->mailerObj->Username = $params['username'];
        $this->mailerObj->Password = $params['password'];
        return $this;
    }

    private function setFrom ( array $from ) {
        $this->mailerObj->From = $from[0];
        $this->mailerObj->FromName = $from[1];
        return $this;
    }

    private function setReplyTo ( array $replyTo ) {
        $this->mailerObj->AddReplyTo($replyTo[0], $replyTo[1]);
        return $this;
    }

    public function send ( $subject, $from, $replyTo=false, $to, $body ) {
        $this->setFrom( $from );
        if ( $replyTo ) {
            $this->setReplyTo( $replyTo );
        }
        $this->mailerObj->AddAddress($to);
        $this->mailerObj->Subject = $subject;
        $this->mailerObj->MsgHTML($body);
        $return = $this->mailerObj->Send();
        $this->mailerObj->clearAddresses();
        $this->mailerObj->clearAttachments();
        $this->stop();
        return $return;
    }

    public function spool(  $subject, $from, $replyTo=false, $to, $body, $directoryPath ) {
        throw new \BadMethodCallException( _e( 'PHPMailer can\'t spool messages' ) );
    }

    /**
     * Remove addresses
     */
    private function clearAddresses() {
        $this->mailerObj->clearAddresses();
    }

    /**
     * Remove attachments
     */
    private function clearAttachments() {
        $this->mailerObj->clearAttachments();
    }

    /**
     * @param array $emails
     * @return $this
     * @throws \phpmailerException
     */
    public function setBcc ( array $emails ) {
        foreach ($emails as $email) {
            $this->mailerObj->addBcc($email);
        }
        return $this;
    }

    /**
     * @param $file
     * @param $attachmentName
     * @return $this
     * @throws \phpmailerException
     */
    public function addAttachment($filePathOrUrl, $attachmentName) {
        if ( self::isUrl($filePathOrUrl) ) {
            $this->mailerObj->addStringAttachment(file_get_contents($filePathOrUrl), $attachmentName );
        } else {
            $this->mailerObj->addAttachment($filePathOrUrl, $attachmentName);
        }
        return $this;
    }

    private function isUrl ( $possibleUrl ) {
        return filter_var($possibleUrl, FILTER_VALIDATE_URL) == TRUE;
    }

    public function stop () {
        return $this->mailerObj->smtpClose();
    }

}
