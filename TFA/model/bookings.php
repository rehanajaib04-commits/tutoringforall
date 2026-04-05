<?php 
class Booking {
    private $slot_id;
    private $teacher_email_address;
    private $student_email_address;
    private $slot_date;
    private $start_time;
    private $end_time;
    private $status;
    private $is_booked;


    function __get($name){
        return $this->$name;
    }


    function __set($name, $value){
        $this->$name = $value; 
    }
}
