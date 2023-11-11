<?php
class Users extends Model{
    public function getAll(){
        $sql = "SELECT * FROM users";
        return $this->db->getItems($sql);
    }
}