<?php
include( dirname( __FILE__ ) . "/xmpp.php");
$conn = new XMPP('talk.google.com', 5222, 'EMAIL', 'TOKEN', 'xmpphp', 'FROMDOMAIN', $printlog=True, $loglevel=4);
$conn->connect();
$conn->processUntil('session_start');
$conn->message('TO_EMAIL', 'This is a test message from XMPP test.');
$conn->disconnect();
