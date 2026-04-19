<?php
session_start();
require_once "../model/user.php";
require_once "../model/dataAccess.php";

// Already logged in? No need to reset
if (isset($_SESSION['email_address'])) {
    header("Location: myprofile.php");
    exit();
}

$step = $_SESSION['reset_step'] ?? 1;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ---------- STEP 1: Look up email ---------- */
    if (isset($_POST['step1'])) {
        $email = trim($_POST['email_address'] ?? '');

        if ($email === '') {
            $error = "Please enter your email address.";
        } else {
            $results = getUserByEmail($email);
            if (!empty($results)) {
                $_SESSION['reset_step']    = 2;
                $_SESSION['reset_email']   = $email;
                $_SESSION['reset_user']    = serialize($results[0]);
                header("Location: reset_password.php");
                exit();
            } else {
                $error = "No account found with that email address.";
            }
        }
    }

    /* ---------- STEP 2: Verify security answer ---------- */
    elseif (isset($_POST['step2'])) {
        if (empty($_SESSION['reset_email']) || empty($_SESSION['reset_user'])) {
            header("Location: reset_password.php");
            exit();
        }

        $answer = trim($_POST['security_answer'] ?? '');
        $user   = unserialize($_SESSION['reset_user']);

        if (strtolower($answer) === strtolower(trim($user->security_answer ?? ''))) {
            $_SESSION['reset_step']     = 3;
            $_SESSION['reset_verified'] = true;
            header("Location: reset_password.php");
            exit();
        } else {
            $error = "Incorrect answer. Please try again.";
        }
    }

    /* ---------- STEP 3: Save new password ---------- */
    elseif (isset($_POST['step3'])) {
        if (empty($_SESSION['reset_verified']) || empty($_SESSION['reset_email'])) {
            header("Location: reset_password.php");
            exit();
        }

        $new_password     = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($new_password === '') {
            $error = "Please enter a new password.";
        } elseif ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $email   = $_SESSION['reset_email'];
            $results = getUserByEmail($email);

            if (!empty($results)) {
                $u = $results[0];

                // Reuses the exact same function as myprofile.php
                updateUserProfile(
                    $email,
                    $u->first_name,
                    $u->last_name,
                    $u->contact_number,
                    $new_password,
                    $u->date_of_birth ?? null,
                    $u->gender ?? null,
                    $u->ethnicity ?? null
                );

                // Clean up
                unset(
                    $_SESSION['reset_step'],
                    $_SESSION['reset_email'],
                    $_SESSION['reset_user'],
                    $_SESSION['reset_verified']
                );

                header("Location: sign_in.php?reset=1");
                exit();
            } else {
                $error = "User not found.";
            }
        }
    }
}

require_once "../view/reset_password_view.php";