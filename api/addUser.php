<?php
post();

$dataRules = ["uname" => "required|string",
    "upass" => "required|string",
    "firstName" => "required|string",
    "lastName" => "required|string"];
validate($_POST,$dataRules);

extract(allowedVars($_POST,$dataRules));

$db = new DAL();
$values = ["id" => uniqid(),
    "username" => $uname,
    "userpass" => password_hash($upass,PASSWORD_DEFAULT),
    "first_name" => $firstName,
    "last_name" => $lastName];
if(!$db->insert("users",$values))
    invalid("Add user failed!");

to("/api/getAllUser");