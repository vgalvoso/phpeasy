<?php
class Model{

    protected $db;

    public function __construct(Sql $db){
        $this->db = $db;
    }

}