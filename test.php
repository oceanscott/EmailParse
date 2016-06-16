<?php
header ('Content-type:text/html charset=UTF-8');
require_once('message_decoder.php');
$decoder=new message_decoder;

$decoder->getEmailInfo($EmailInfo);//$EmailInfo=array('Body'=>array(), 'from'=>array(), 'to'=>array(), 'subject'=>array(), 'date'=>array());
var_dump($EmailInfo);
?>