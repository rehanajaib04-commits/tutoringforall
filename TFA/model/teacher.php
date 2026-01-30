<?php 
class Teacher {
    private $teacher_id;
    private $teacher_type;
    private $bio;
    private $qualifications;
    private $rating;
     private $subjects;
     private $email_address;
     private $contact_number;


    function __get($name){
        return $this->$name;
    }

    function __set($name, $value){
        $this->$name = $value; 
    }
}