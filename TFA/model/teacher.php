<?php 
class Teacher {
    private $teacher_id;
    private $teacher_type;

    function __get($name){
        return $this->$name;
    }

    function __set($name, $value){
        $this->name = $value;
    }

}