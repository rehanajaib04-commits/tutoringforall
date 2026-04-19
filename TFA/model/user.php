<?php

class User {
    public $email_address;
    public $first_name;
    public $last_name;
    public $contact_number;
    public $user_type;
    public $password;
    public $security_question;
    public $security_answer;
    public $date_of_birth;   // Added for signup
    public $gender;          // Added for signup
    public $ethnicity;       // Added for signup

    // Optional helper methods
    public function getFullName() {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function isStudent() {
        return strtolower($this->user_type ?? '') === 'student';
    }

    public function isParent() {
        return strtolower($this->user_type ?? '') === 'parent';
    }

    public function isTeacher() {
        return strtolower($this->user_type ?? '') === 'teacher';
    }

    public function isAdmin() {
        return strtolower($this->user_type ?? '') === 'admin';
    }
}