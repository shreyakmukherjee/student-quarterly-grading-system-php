<?php
Class DBConnection{
    protected $db;
    function __construct(){
        $this->db = new mysqli('localhost','root','','sqgs_db');
        if(!$this->db){
            die("Database Connection Failed. Error: ".$this->db->error);
        }
    }
    function db_connect(){
        return $this->db;
    }
    function __destruct(){
         $this->db->close();
    }
}
function format_num($number = '', $decimal=''){
    if(is_numeric($number)){
        $ex = explode('.',$number);
        $dec_len = isset($ex[1]) ? strlen($ex[1]) : 0;
        if(is_numeric($decimal)){
            return number_format($number,$decimal);
        }else{
            return number_format($number,$dec_len);
        }
    }else{
        return 'Invalid Input';
    }
}
$database = new DBConnection();
$conn= $database->db_connect();