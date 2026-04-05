<?php 
class Address{
 private $postcode;
 private $addressline1;
 private $addressline2;
private $town;
 private $county;

    function __get($name){
        return $this->$name;
    }

    function __set($name, $value){
        $this->$name = $value;
    }
}
?>