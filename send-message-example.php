<?php

error_reporting(E_ALL);

include('msn.php');

$sendMsg = new MSN();

$sendMsg->simpleSend( 'FROM_USER', 'MOTDEPASSE', 'TO_USER','MESSAGE');

echo $sendMsg->result.' '.$sendMsg->error;
