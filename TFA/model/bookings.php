<?php 
class Booking {
    private $booking_id;
    private $teacher_id;
    private $student_id;
    private $date;
    private $start_time;
    private $status;


    function __get($name){
        return $this->$name;
    }


    function __set($name, $value){
        $this->$name = $value; 
    }
}