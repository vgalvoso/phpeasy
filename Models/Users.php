<?php
class Users extends Model{

    public function __construct(DAL $db){
        parent::__construct($db);
        $this->table = "users";
    }
}