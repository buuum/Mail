<?php

namespace Buuum\MailerHandler;

use Buuum\MailerInterface;

class SwiftMailerHandler implements MailerInterface {

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Swift_Message
     */
    private $message;
    /**
     * Array that contains connection params
     * @var array
     */
    private static $transportParams;

    public function setMailer ( \Swift_Mailer $mailer ) {
        $this->mailer = $mailer;
        return $this;
    }

    public static function createMailerInstance ( $transportObj ) {
        return \Swift_Mailer::newInstance($transportObj);
    }

    public static function createSmtpTransportInstance ( array $params ) {
        return \Swift_SmtpTransport::newInstance(
                                        $params['host'],
                                        $params['port'],
                                        $params['smtpsecure']
                                     )
                                     ->setUsername($params['username'])
                                     ->setPassword($params['password']);
    }

    public static function createSpoolTransportInstance ( $spoolDirectoryPath ) {
        $spool = new \Swift_FileSpool($spoolDirectoryPath);
        return \Swift_SpoolTransport::newInstance($spool);
    }

    public function setConfig ( array $params ) {
        self::$transportParams = $params;
    }

    public function send ( $subject, $from, $replyTo=false, $to, $body ) {
        $this->setMailer(
            $this->createMailerInstance(
                self::createSmtpTransportInstance( self::$transportParams )
            )
        );
        $result = $this->mailer->send(
                      $this->getMessage( $subject, $from, $replyTo=false, $to, $body )
                  );
        $this->removeSendedMessage();
        return $result;
    }

    private function removeSendedMessage () {
        $this->message = NULL;
    }

    private function getMessage ( $subject, $from, $replyTo=false, $to, $body ) {
        if ( $this->messageIsNotCreatedYet() ) {
            $this->createEmptyMessageInstance();
        }
        $this->message->setSubject($subject)
                      ->setFrom(array($from[0] => $from[1]))
                      ->setTo(array($to))
                      ->setBody($body)
                      ->setContentType("text/html");

        if ( $replyTo ) {
            $this->message->setReplyTo( $replyTo );
        }
        return $this->message;
    }

    private static function ifDirectoryDoesntExistsTrhowInvalidArgumentExcetion( $directoryPath ) {
        if ( ! file_exists($directoryPath) ) {
            throw new \InvalidArgumentException( 'The provided directory doesn\'t exists.' );
        }
    }

    public function spool ($subject, $from, $replyTo=false, $to, $body, $directoryPath ) {
        self::ifDirectoryDoesntExistsTrhowInvalidArgumentExcetion( $directoryPath );
        $this->setMailer(
            $this->createMailerInstance(
                self::createSpoolTransportInstance( $directoryPath )
            )
        );
        $result = $this->mailer->send(
                      $this->getMessage( $subject, $from, $replyTo=false, $to, $body )
                  );
        $this->removeSendedMessage();
        return $result;
    }

    public static function sendSpooledMessages ( $directoryPath, $messageLimit=100, $timeLimit=60 ) {
        self::ifDirectoryDoesntExistsTrhowInvalidArgumentExcetion( $directoryPath );
        $transport = self::createSpoolTransportInstance( $directoryPath );
        $spool = $transport->getSpool();
        $spool->setMessageLimit($messageLimit);
        $spool->setTimeLimit($timeLimit);
        return $spool->flushQueue(
            self::createSmtpTransportInstance( self::$transportParams )
        );
    }

    private function createEmptyMessageInstance () {
        $this->message = \Swift_Message::newInstance();
    }

    private function ifNoThisMessageCreateIt () {
        if (! $this->messageIsNotCreatedYet ) {
            $this->createEmptyMessageInstance();
        }
    }

    private function messageIsNotCreatedYet () {
        return $this->message == NULL;
    }

    public function setBcc( array $emails ) {
        $this->ifNoThisMessageCreateIt();
        return $this->message->setBcc($emails);
    }

    /**
     * @param $filePathOrUrl
     * @param $attachmentName
     */
    public function addAttachment($filePathOrUrl, $attachmentName) {
        $this->ifNoThisMessageCreateIt();
        $this->message->attach(
            \Swift_Attachment::fromPath($filePathOrUrl)->setFilename($attachmentName)
        );
    }

    public function stop () {
        return $this->mailer->getTransport()->stop();
    }

}
