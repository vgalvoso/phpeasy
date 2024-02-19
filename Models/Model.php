<?php

namespace Models;

use Core\DAL;

class Model{

    protected $db;
    protected $table;

    protected function __construct(DAL $db){
        $this->db = $db;
    }

    public function getAll(string $condition = "1=1",array $params = []){
        $sql = "SELECT * FROM $this->table WHERE $condition";
        return $this->db->getItems($sql,$params);
    }

    public function get($id){
        $sql = "SELECT * FROM $this->table WHERE id=:id";
        return $this->db->getItem($sql,["id" => $id]);
    }

    public function add($values){
        return $this->db->insert($this->table,$values);
    }

    public function delete($id){
        return $this->db->delete($this->table,"id=:id",["id"=>$id]);
    }
}

//EOF
