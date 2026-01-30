<?php 
class Parent{
    private $email_address; 
    private $parent_type;  // Add $ here

    function __get($name){
        return $this->$name;
    }

    function __set($name, $value){
        $this->$name = $value;
    }
}
?>