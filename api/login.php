<?php

use Core\DAL;

use function Core\Helper\{error,getRequestBody,redirect,startAPI};
use function Core\Validator\validate;

startAPI();

function post(){

    $rq = getRequestBody();

    //Input Validations
    $inputRules = [
        "username" => "required",
        "password" => "required"
    ];

    if(!validate($rq,$inputRules))
        error("Invalid Request body!");

    //Variable initialization
    $rq = (object) $rq;
    $username = $rq->username;
    $password = $rq->password;

    $db = new DAL();
    
    $query = "SELECT password FROM users WHERE username=:username";
    $params = ["username" => $username];
    if(!$user = $db->getItem($query,$params))
        redirect("login/login_failed");
    if(password_verify($password,$user->password))
        redirect("dashboard");
    
}

//EOF
