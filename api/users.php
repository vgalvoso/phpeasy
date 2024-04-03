<?php

use Core\DAL;

use function Core\Helper\error;
use function Core\Helper\getRequestBody;
use function Core\Helper\response;
use function Core\Helper\startAPI;

startAPI();

function get(){
    $db = new DAL();
    //GET user by id
    if(defined('URI_PARAM')){
        $query = "SELECT * FROM users WHERE id = :id";
        $param = ["id" => URI_PARAM];
        if(!$user = $db->getItem($query,$param))
            response([]);
        response([$user]);
    }
    //GET All users
    $query = "SELECT * FROM users";
    $users = $db->getItems($query);
    response($users);
}

function post(){
    $db = new DAL();
    $rq = (object)getRequestBody();
    $values = [
        "username" => $rq->username,
        "firstname" => $rq->firstname,
        "lastname" => $rq->lastname,
        "usertype" => $rq->usertype,
        "password" => password_hash($rq->password,PASSWORD_BCRYPT)
    ];
    if(!$db->insert("users",$values))
        error($db->getError());
    response("New User added!");
}

function delete(){
    if(!defined('URI_PARAM'))
        error("Invalid Request! Please specify user id");
    $db = new DAL();
    $id = URI_PARAM;
    if(!$db->delete("users","id=:id",["id" => $id]))
        error($db->getError());
    response("User Deleted Successfuly!");
}

function patch(){
    if(!defined('URI_PARAM'))
        error("Invalid Request! Please specify user id");
    $db = new DAL();
    $id = URI_PARAM;
    $rq = (object)getRequestBody();
    $values = [
        "firstname" => $rq->firstname,
        "lastname" => $rq->lastname];
    $params = ["id" => $id];

    $db = new DAL();

    if(!$db->update("users","id=:id",$values,$params))
        error($db->getError());
    response("User Updated Successfuly");
}

//EOF
