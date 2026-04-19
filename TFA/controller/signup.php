<?php
require_once '../model/dataAccess.php';
require_once '../model/user.php';
require_once '../model/student.php';
require_once '../model/parent.php';

$error_message = '';
$step = '1';
$student_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_step = $_POST['step'] ?? '1';

    if ($posted_step === '1') {
        // Step 1 fields
        $student_first_name     = trim($_POST['student_first_name'] ?? '');
        $student_last_name      = trim($_POST['student_last_name'] ?? '');
        $student_contact        = trim($_POST['student_contact_number'] ?? '');
        $student_email          = trim($_POST['student_email_address'] ?? '');
        $student_password       = $_POST['student_password'] ?? '';
        $student_security_q     = $_POST['student_security_question'] ?? '';
        $student_security_a     = trim($_POST['student_security_answer'] ?? '');
        $student_type           = $_POST['student_type'] ?? '';
        $student_dob            = trim($_POST['student_date_of_birth'] ?? '');
        $student_gender         = trim($_POST['student_gender'] ?? '');
        $student_ethnicity      = trim($_POST['student_ethnicity'] ?? '');
        $post_code              = trim($_POST['post_code'] ?? '');
        $address_line1          = trim($_POST['address_line1'] ?? '');
        $addressline2           = trim($_POST['addressline2'] ?? '');
        $town                   = trim($_POST['town'] ?? '');
        $county                 = trim($_POST['county'] ?? '');

        // Validation
        $errors = [];
        if (empty($student_first_name) || empty($student_last_name) ||
            empty($student_email) || empty($student_password) || empty($student_security_a)) {
            $errors[] = "All required fields must be filled.";
        }
        if (!empty($student_email) && !filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid student email address.";
        }
        if (empty($student_type)) {
            $errors[] = "Please select a student type.";
        }
        if (empty($student_dob)) {
            $errors[] = "Date of birth is required.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $student_dob)) {
            $errors[] = "Invalid date format.";
        } else {
            $dob = new DateTime($student_dob);
            $today = new DateTime();
            $age = $today->diff($dob)->y;
            if ($age > 18) {
                $errors[] = "Student must be under 18 years old.";
            }
        }
        $allowed_genders = ['Male', 'Female', 'Other', 'Prefer not to say', ''];
        if (!in_array($student_gender, $allowed_genders)) {
            $errors[] = "Invalid gender selection.";
        }

        if (!empty($errors)) {
            $error_message = implode(' ', $errors);
            $step = '1';
        } else {
            $student_data = $_POST;
            $step = '1.5'; // EVERYONE goes to the parent choice step now
        }

    } elseif ($posted_step === '1.5') {
        $add_parent = $_POST['add_parent'] ?? '';
        $student_data = $_POST;
        $student_type_in_data = $student_data['student_type'] ?? '';

        if ($add_parent === 'new') {
            $step = '2';
        } elseif ($add_parent === 'solo') {
            // Only College/Sixth Form can go solo
            if ($student_type_in_data !== 'Colleges, Sixth Form') {
                $error_message = "You must have a parent or guardian.";
                $step = '1.5';
            } else {
                // Create solo student account
                $student_first_name = trim($student_data['student_first_name'] ?? '');
                $student_last_name  = trim($student_data['student_last_name'] ?? '');
                $student_contact    = trim($student_data['student_contact_number'] ?? '');
                $student_email      = trim($student_data['student_email_address'] ?? '');
                $student_password   = $student_data['student_password'] ?? '';
                $student_security_q = $student_data['student_security_question'] ?? '';
                $student_security_a = trim($student_data['student_security_answer'] ?? '');
                $student_type       = $student_data['student_type'] ?? '';
                $student_dob        = trim($student_data['student_date_of_birth'] ?? '');
                $student_gender     = trim($student_data['student_gender'] ?? '');
                $student_ethnicity  = trim($student_data['student_ethnicity'] ?? '');
                $post_code          = trim($student_data['post_code'] ?? '');
                $address_line1      = trim($student_data['address_line1'] ?? '');
                $addressline2       = trim($student_data['addressline2'] ?? '');
                $town               = trim($student_data['town'] ?? '');
                $county             = trim($student_data['county'] ?? '');

                $existing_student = getUserByEmail($student_email);
                if (!empty($existing_student)) {
                    $error_message = "Student email is already registered.";
                    $step = '1.5';
                } else {
                    try {
                        $pdo->beginTransaction();

                        $student_created = addUser(
                            $student_first_name, $student_last_name, $student_contact,
                            $student_email, 'Student', $student_password,
                            $student_security_q, $student_security_a,
                            $student_dob, $student_gender, $student_ethnicity
                        );

                        if ($student_created) {
                            addStudent($student_email, $student_type);

                            if (!empty($post_code) && !empty($address_line1)) {
                                try {
                                    addAddress($post_code, $address_line1, $addressline2 ?: null, $town ?: null, $county ?: null);
                                    linkUserAddress($student_email, $address_line1, $post_code);
                                } catch (PDOException $addrEx) {
                                    error_log("Address insert failed: " . $addrEx->getMessage());
                                }
                            }

                            $pdo->commit();
                            header("Location: sign_in.php?registered=1");
                            exit();
                        } else {
                            $pdo->rollBack();
                            $error_message = "Failed to create account. Please try again.";
                            $step = '1.5';
                        }
                    } catch (PDOException $e) {
                        if ($pdo->inTransaction()) $pdo->rollBack();
                        $error_message = "Database error. Please try again later.";
                        $step = '1.5';
                    } catch (Exception $e) {
                        if ($pdo->inTransaction()) $pdo->rollBack();
                        $error_message = "An unexpected error occurred. Please try again.";
                        $step = '1.5';
                    }
                }
            }
        } elseif ($add_parent === 'existing') {
            // Link to existing parent
            $existing_parent_email = trim($_POST['existing_parent_email'] ?? '');
            $student_first_name = trim($student_data['student_first_name'] ?? '');
            $student_last_name  = trim($student_data['student_last_name'] ?? '');
            $student_contact    = trim($student_data['student_contact_number'] ?? '');
            $student_email      = trim($student_data['student_email_address'] ?? '');
            $student_password   = $student_data['student_password'] ?? '';
            $student_security_q = $student_data['student_security_question'] ?? '';
            $student_security_a = trim($student_data['student_security_answer'] ?? '');
            $student_type       = $student_data['student_type'] ?? '';
            $student_dob        = trim($student_data['student_date_of_birth'] ?? '');
            $student_gender     = trim($student_data['student_gender'] ?? '');
            $student_ethnicity  = trim($student_data['student_ethnicity'] ?? '');
            $post_code          = trim($student_data['post_code'] ?? '');
            $address_line1      = trim($student_data['address_line1'] ?? '');
            $addressline2       = trim($student_data['addressline2'] ?? '');
            $town               = trim($student_data['town'] ?? '');
            $county             = trim($student_data['county'] ?? '');

            if (empty($existing_parent_email)) {
                $error_message = "Please enter the parent/guardian's email address.";
                $step = '1.5';
            } elseif (!filter_var($existing_parent_email, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Please enter a valid email address.";
                $step = '1.5';
            } elseif (strcasecmp($student_email, $existing_parent_email) === 0) {
                $error_message = "Student and parent must have different email addresses.";
                $step = '1.5';
            } else {
                $parent_user = getUserByEmail($existing_parent_email);
                $is_parent = false;
                if (!empty($parent_user)) {
                    $parent_check = $pdo->prepare("SELECT COUNT(*) FROM parents WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))");
                    $parent_check->execute([$existing_parent_email]);
                    $is_parent = (int)$parent_check->fetchColumn() > 0;
                }

                if (empty($parent_user) || !$is_parent) {
                    $error_message = "No parent account found with that email address.";
                    $step = '1.5';
                } else {
                    $existing_student = getUserByEmail($student_email);
                    if (!empty($existing_student)) {
                        $error_message = "Student email is already registered.";
                        $step = '1.5';
                    } else {
                        try {
                            $pdo->beginTransaction();

                            $student_created = addUser(
                                $student_first_name, $student_last_name, $student_contact,
                                $student_email, 'Student', $student_password,
                                $student_security_q, $student_security_a,
                                $student_dob, $student_gender, $student_ethnicity
                            );

                            if ($student_created) {
                                addStudent($student_email, $student_type);
                                linkStudentParent($student_email, $existing_parent_email);

                                if (!empty($post_code) && !empty($address_line1)) {
                                    try {
                                        addAddress($post_code, $address_line1, $addressline2 ?: null, $town ?: null, $county ?: null);
                                        linkUserAddress($student_email, $address_line1, $post_code);
                                    } catch (PDOException $addrEx) {
                                        error_log("Address insert failed: " . $addrEx->getMessage());
                                    }
                                }

                                $pdo->commit();
                                header("Location: sign_in.php?registered=1");
                                exit();
                            } else {
                                $pdo->rollBack();
                                $error_message = "Failed to create account. Please try again.";
                                $step = '1.5';
                            }
                        } catch (PDOException $e) {
                            if ($pdo->inTransaction()) $pdo->rollBack();
                            $error_message = "Database error. Please try again later.";
                            $step = '1.5';
                        } catch (Exception $e) {
                            if ($pdo->inTransaction()) $pdo->rollBack();
                            $error_message = "An unexpected error occurred. Please try again.";
                            $step = '1.5';
                        }
                    }
                }
            }
        } else {
            $error_message = "Please select whether you want to add a parent or guardian.";
            $step = '1.5';
        }

    } elseif ($posted_step === '2') {
        // Step 2 fields
        $student_first_name     = trim($_POST['student_first_name'] ?? '');
        $student_last_name      = trim($_POST['student_last_name'] ?? '');
        $student_contact        = trim($_POST['student_contact_number'] ?? '');
        $student_email          = trim($_POST['student_email_address'] ?? '');
        $student_password       = $_POST['student_password'] ?? '';
        $student_security_q     = $_POST['student_security_question'] ?? '';
        $student_security_a     = trim($_POST['student_security_answer'] ?? '');
        $student_type           = $_POST['student_type'] ?? '';
        $student_dob            = trim($_POST['student_date_of_birth'] ?? '');
        $student_gender         = trim($_POST['student_gender'] ?? '');
        $student_ethnicity      = trim($_POST['student_ethnicity'] ?? '');
        $post_code              = trim($_POST['post_code'] ?? '');
        $address_line1          = trim($_POST['address_line1'] ?? '');
        $addressline2           = trim($_POST['addressline2'] ?? '');
        $town                   = trim($_POST['town'] ?? '');
        $county                 = trim($_POST['county'] ?? '');

        $parent_first_name      = trim($_POST['first_name'] ?? '');
        $parent_last_name       = trim($_POST['last_name'] ?? '');
        $parent_contact         = trim($_POST['contact_number'] ?? '');
        $parent_email           = trim($_POST['email_address'] ?? '');
        $parent_password        = $_POST['password'] ?? '';
        $parent_security_q      = $_POST['security_question'] ?? '';
        $parent_security_a      = trim($_POST['security_answer'] ?? '');
        $parent_type            = $_POST['parent_type'] ?? '';
        $parent_gender          = trim($_POST['parent_gender'] ?? '');
        $parent_ethnicity       = trim($_POST['parent_ethnicity'] ?? '');

        // Validation
        $errors = [];
        if (empty($parent_first_name) || empty($parent_last_name) ||
            empty($parent_email) || empty($parent_password) || empty($parent_security_a)) {
            $errors[] = "All required fields must be filled.";
        }
        if (!empty($parent_email) && !filter_var($parent_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid parent email address.";
        }
        if (strcasecmp($student_email, $parent_email) === 0) {
            $errors[] = "Student and parent must have different email addresses.";
        }
        if (empty($parent_type)) {
            $errors[] = "Please select a relationship to the student.";
        }
        $allowed_genders = ['Male', 'Female', 'Other', 'Prefer not to say', ''];
        if (!in_array($parent_gender, $allowed_genders)) {
            $errors[] = "Invalid gender selection for parent.";
        }

        if (!empty($errors)) {
            $error_message = implode(' ', $errors);
            $step = '2';
            $student_data = $_POST;
        } else {
            // Check existing users
            $existing_student = getUserByEmail($student_email);
            $existing_parent  = getUserByEmail($parent_email);

            if (!empty($existing_student)) {
                $error_message = "Student email is already registered.";
                $step = '2';
                $student_data = $_POST;
            } elseif (!empty($existing_parent)) {
                $error_message = "Parent email is already registered.";
                $step = '2';
                $student_data = $_POST;
            } else {
                try {
                    $pdo->beginTransaction();

                    // Create parent user
                    $parent_created = addUser(
                        $parent_first_name, $parent_last_name, $parent_contact,
                        $parent_email, 'Parent', $parent_password,
                        $parent_security_q, $parent_security_a,
                        null, $parent_gender, $parent_ethnicity
                    );

                    // Create student user
                    $student_created = addUser(
                        $student_first_name, $student_last_name, $student_contact,
                        $student_email, 'Student', $student_password,
                        $student_security_q, $student_security_a,
                        $student_dob, $student_gender, $student_ethnicity
                    );

                    if ($parent_created && $student_created) {
                        addParent($parent_email, $parent_type);
                        addStudent($student_email, $student_type);
                        linkStudentParent($student_email, $parent_email);

                        // Address handling
                        if (!empty($post_code) && !empty($address_line1)) {
                            try {
                                addAddress($post_code, $address_line1, $addressline2 ?: null, $town ?: null, $county ?: null);
                                linkUserAddress($student_email, $address_line1, $post_code);
                                linkUserAddress($parent_email, $address_line1, $post_code);
                            } catch (PDOException $addrEx) {
                                error_log("Address insert failed: " . $addrEx->getMessage());
                            }
                        }

                        $pdo->commit();
                        header("Location: sign_in.php?registered=1");
                        exit();
                    } else {
                        $pdo->rollBack();
                        $error_message = "Failed to create accounts. Please try again.";
                        $step = '2';
                        $student_data = $_POST;
                    }
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $error_message = "Database error. Please try again later.";
                    $step = '2';
                    $student_data = $_POST;
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error_message = "An unexpected error occurred. Please try again.";
                    $step = '2';
                    $student_data = $_POST;
                }
            }
        }
    }
}

include '../view/signupView.php';