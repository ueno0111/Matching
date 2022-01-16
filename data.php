<?php

    require_once('matching.php');
    require_once('database.php');

    $contact = new Database();
    $login = new Database();
    $signup = new Database();
    $image = new Database();

    $contact->Contact();
    $login->Login();
    $signup->Signup();
    $image->Image();

?>