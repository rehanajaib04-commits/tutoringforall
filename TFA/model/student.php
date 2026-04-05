<?php 
class Student{
    private $email_address; 
    private $student_type; 

    function __get($name){
        return $this->$name;
    }

    function __set($name, $value){
        $this->$name = $value;
    }
}
?>