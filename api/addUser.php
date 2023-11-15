<?php
$db = new DAL();

$dataRules = ["username" => "required|string",
    "userPass" => "required|string",
    "firstName" => "required|string",
    "lastName" => "required|string"];
extract(allowedVars($_POST,$dataRules));

$values = ["username" => $username,
    "password" => password_hash($userPass,PASSWORD_DEFAULT),
    "firstname" => $firstName,
    "lastname" => $lastName];

if(!$db->insert("users",$values))
    invalid($db->getError());

exit($db->lastId());