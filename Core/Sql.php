<?php
class Sql{

    protected $conn;
    protected $db;
    public $error;
    public $query;
    
    /**
     * Connect to database
     *
     * @param string $dbase Database credentials set in connect.php
     * Below here for custom connection(not set in connect.php)
     * @param string $server Database server ip  
     * @param string $user Database username  
     * @param string $pass Database password  
     * @param string $dbname Database name  
     * @param string $driver Database driver(mysql,mssql)  
     * 
     * @author Van
     */
    public function __construct($dbase="default",$server="",$user="",$pass="",$dbname="",$driver=""){
        require "Config/Database.php";
        if($dbase != null){
            $server = $db[$dbase]["server"];
            $user = $db[$dbase]["user"];
            $pass = $db[$dbase]["pass"];
            $dbname = $db[$dbase]["dbname"];
            $driver = $db[$dbase]["driver"];
        }
        try{
            $this->conn = new PDO("$driver:host=$server;dbname=$dbname;",$user,$pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }catch(PDOException $e){
            exit($this->error = $e);
        }
    }

    public function getDriver(){
        return $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    public function getError(){
        return $this->error;
    }
    
    /**
     * Select single row query
     *
     * @param string $query Select statement
     * @param array $inputs Parameters for prepared statement
     *       null(default)/("param"=>$value) 
     * 
     * @author Van
     * 
     * @return Object|false  
     */
    public function getItem($query,$inputs=null){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute($inputs);
            return $stmt->fetch(PDO::FETCH_OBJ);
        }catch(PDOException $e){
            $this->error = $e;
            return false;
        }
    }

    /**
     * Select multiple items
     *
     * @param string $query Select statement
     * @param array $inputs Parameters for prepared statement
     *       null(default)/("param"=>$value) 
     * 
     * @author Van
     * 
     * @return array|false  
     */
    public function getItems($query,$inputs=null){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute($inputs);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }catch(PDOException $e){
            $this->error = $e;
            return false;
        }
    }

    //insert/update/delete and other queries
    public function exec($query,$inputs=null){
        try{
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($inputs);
        }catch(PDOException $e){
            $this->error = $e;
            return false;
        }
    }
    
    /**
     * Execute insert statement
     * 
     * @param string $table Table name to insert into
     * @param array $values Array of values to insert eg. ["fName" => "Eren","lName" => "Jaeger"]
     */
    public function insert($table,$values){
        try{
            $keys = array_keys($values);
            $newKeys = [];
            foreach($keys as $key){
                array_push($newKeys,":$key");
            }
            $refs = implode(",",$newKeys);
            $fields = implode(",",$keys);
            $query = "INSERT INTO $table($fields) VALUES($refs);";
           $stmt = $this->conn->prepare($query);
            return $stmt->execute($values);
        }catch(PDOException $e){
            $this->error = $e;
            return false;
        }
    }

    /**
     * Executes update statement
     * 
     * @param string $table The table to update
     * @param array $condition Conditions eg. id = :id
     * @param array $values Values to update eg . ["age" => 27]
     * @param array $params Values for conditions eg . ["id" => 1]
     * @return boolean
     */
    public function update($table,$condition,$values,$params=[]){
        try{
            $keys = array_keys($values);
            $newKeys = [];
            foreach($keys as $key){
                array_push($newKeys,"$key = :$key");
            }
            $newParams = array_merge($values,$params);
            $refs = implode(",",$newKeys);
            $condition = empty($condition) ? "" : "WHERE ".$condition;
            $query = "UPDATE $table SET $refs $condition ;";
           $stmt = $this->conn->prepare($query);
            return $stmt->execute($newParams);
        }catch(PDOException $e){
            $this->error = $e;
            return false;
        }
    }

    public function lastId($field=null){
        return $this->conn->lastInsertId($field);
    }

    public function startTrans(){
        if($this->getDriver() == "mssql")
            return $this->exec("BEGIN TRANSACTION");
        return $this->conn->beginTransaction();
    }

    public function commit(){
        if($this->getDriver() == "mssql")
            return $this->exec("COMMIT");
        return $this->conn->commit();
    }

    public function rollback(){
        if($this->getDriver() == "mssql")
            return $this->exec("if @@TRANCOUNT > 0 ROLLBACK");
        return $this->conn->rollBack();
    } 
}