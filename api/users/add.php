<?php
post();

$db = new DAL();
$users = new Users($db);

$values = ["username" => $username,
    "firstname" => $firstname,
    "lastnamr" => $lastname];

if(!$users->create($values))
    invalid("Can't add user!");