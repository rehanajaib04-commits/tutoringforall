<?php 
class User{
    private $user_id;
    private $first_name;
    private $last_name;
    private $contact_number;
    private $email_address;
    private $user_type;
    private $password;
    private $security_question;
    private $security_answer;

    function __get($name){
        return $this->$name;
    }

    function __set($name, $value){
        $this->$name = $value;
    }

}