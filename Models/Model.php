<?php
class Model{

    protected $db;

    public function __construct(DAL $db){
        $this->db = $db;
    }

}