<?php

use Core\DAL;
use Models\Users;

use function Core\Helper\getRequestBody;
use function Core\Helper\response;
use function Core\Helper\startAPI;
use function Core\Validator\validate;

startAPI();

function get(){
    $db = new DAL();
    $usersModel = new Users($db);

    if(defined("URI_PARAM")){
        if(!$user = $usersModel->get(URI_PARAM))
            response("User not found!",404);
        response([$user]);
    }

    $users = $usersModel->getAll();
    response($users);
}

function post(){
    $db = new DAL();
    $db->startTrans();
    $users = new Users($db);

    $postData = getRequestBody();
    validate($postData,[
        "username" => "required|string",
        "password" => "required|string",
        "firstname" => "required|string",
        "lastname" => "required|string"
    ]);
    
    $values = ["username" => $postData["username"],
        "password" => password_hash($postData["password"],PASSWORD_BCRYPT),
        "firstname" => $postData["firstname"],
        "lastname" => $postData["lastname"]];

    if(!$users->add($values))
        response("Can't add user!".$db->getError(),400);
    
    $db->commit();
    response(["status" => "Success"]);
}

//EOF
