<?php
use Ramsey\Uuid\Uuid;

$dataRules = ["uname" => "required|string",
    "upass" => "required|string",
    "fullname" => "required|string"];
validate($_POST,$dataRules);

$post = $_POST;
$uname = esc($post['uname']);
$upass = esc($post['upass']);
$fullname = esc($post['fullname']);

$db = new DAL();
$values = ["id" => esc(Uuid::uuid4()->toString()),
    "username" => $uname,
    "userpass" => password_hash($upass,PASSWORD_DEFAULT),
    "Name" => $fullname];
if(!$db->insert("users",$values))
    invalid("Add user failed!");

to("api/getAllUser");