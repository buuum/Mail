<?php

namespace Buuum;

interface MailerInterface {
    public function send ( $subject, $from, $replyTo=false, $to, $body );
    public function setConfig( array $params );
    public function spool(  $subject, $from, $replyTo=false, $to, $body, $directoryPath );
    public function stop();
    public function setBcc( array $emails );
    public function addAttachment( $filePathOrUrl, $atachmentName );
}
