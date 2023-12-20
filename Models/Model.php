<?php
class Model{

    protected $db;
    protected $table;

    protected function __construct(DAL $db){
        $this->db = $db;
    }

    public function getAll(){
        $sql = "SELECT * FROM $this->table";
        return $this->db->getItems($sql);
    }

    public function create($values){
        return $this->db->insert($this->table,$values);
    }

    public function get($id){
        $sql = "SELECT * FROM $this->table WHERE id=:id";
        return $this->db->getItem($sql,["id" => $id]);
    }

    public function delete($id){
        return $this->db->delete($this->table,"id=:id",["id"=>$id]);
    }
}