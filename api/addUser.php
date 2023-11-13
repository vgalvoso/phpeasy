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
    "password" => password_hash($upass,PASSWORD_BCRYPT,['cost' => 12]),
    "firstname" => $firstName,
    "lastname" => $lastName];
if(!$db->insert('users',$values))
    invalid("Add user failed!");

//to("/api/getAllUser");