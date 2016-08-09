<?php

include('phpdbdoc.php');

$doc = new phpdbdoc();
$doc->setUserdb("root");
$doc->setLinkdb('localhost');
$doc->setPassword('toor');
$doc->setDataBase('gestorfox');
$doc->DBConnect();
$doc->getDoc();
?>
