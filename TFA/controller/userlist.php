<?php
session_start();

require_once "../model/user.php";
require_once "../model/dataAccess.php";

// Redirect if not logged in
if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=userlist.php");
    exit();
}

// Ensure user is admin
if (strtolower(trim($_SESSION['user_type'] ?? '')) !== 'admin') {
    header("Location: myprofile.php");
    exit();
}

// Flash message handling
$message = $_SESSION['userlist_flash']['message'] ?? '';
$message_type = $_SESSION['userlist_flash']['type'] ?? 'info';
unset($_SESSION['userlist_flash']);

$search = trim($_POST['search'] ?? ($_GET['search'] ?? ''));

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $delete_email = trim($_POST['delete_user_email'] ?? '');
    $current_search = trim($_POST['current_search'] ?? '');

    if ($delete_email === '') {
        $_SESSION['userlist_flash'] = [
            'message' => 'Please choose a user to delete.',
            'type' => 'danger',
        ];
    } elseif (strcasecmp($delete_email, trim($_SESSION['email_address'])) === 0) {
        $_SESSION['userlist_flash'] = [
            'message' => 'You cannot delete the account you are currently signed in with.',
            'type' => 'danger',
        ];
    } else {
        try {
            if (deleteUserByEmail($delete_email)) {
                $_SESSION['userlist_flash'] = [
                    'message' => 'User deleted successfully.',
                    'type' => 'success',
                ];
            } else {
                $_SESSION['userlist_flash'] = [
                    'message' => 'User not found or could not be deleted.',
                    'type' => 'danger',
                ];
            }
        } catch (Throwable $exception) {
            $_SESSION['userlist_flash'] = [
                'message' => 'Unable to delete user: ' . $exception->getMessage(),
                'type' => 'danger',
            ];
        }
    }

    $redirect = 'userlist.php';
    if ($current_search !== '') {
        $redirect .= '?search=' . urlencode($current_search);
    }

    header('Location: ' . $redirect);
    exit();
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $target_email = trim($_POST['target_user_email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $current_search = trim($_POST['current_search'] ?? '');

    if ($target_email === '') {
        $_SESSION['userlist_flash'] = [
            'message' => 'Please select a user to change password.',
            'type' => 'danger',
        ];
    } elseif ($new_password === '') {
        $_SESSION['userlist_flash'] = [
            'message' => 'Please enter a new password.',
            'type' => 'danger',
        ];
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['userlist_flash'] = [
            'message' => 'Passwords do not match.',
            'type' => 'danger',
        ];
    } elseif (strcasecmp($target_email, trim($_SESSION['email_address'])) === 0) {
        $_SESSION['userlist_flash'] = [
            'message' => 'Use My Profile to change your own password.',
            'type' => 'warning',
        ];
    } else {
        try {
            // Get user details to preserve other fields
            $userResults = getUserByEmail($target_email);
            if (!empty($userResults)) {
                $targetUser = $userResults[0];
                // Update with new password - using updateUserProfile pattern from myprofile.php
                updateUserProfile(
                    $target_email,
                    $targetUser->first_name ?? '',
                    $targetUser->last_name ?? '',
                    $targetUser->contact_number ?? '',
                    $new_password,
                    $targetUser->date_of_birth ?? null,
                    $targetUser->gender ?? '',
                    $targetUser->ethnicity ?? ''
                );
                $_SESSION['userlist_flash'] = [
                    'message' => 'Password changed successfully for ' . htmlspecialchars($target_email) . '.',
                    'type' => 'success',
                ];
            } else {
                $_SESSION['userlist_flash'] = [
                    'message' => 'User not found.',
                    'type' => 'danger',
                ];
            }
        } catch (Throwable $exception) {
            $_SESSION['userlist_flash'] = [
                'message' => 'Unable to change password: ' . $exception->getMessage(),
                'type' => 'danger',
            ];
        }
    }

    $redirect = 'userlist.php';
    if ($current_search !== '') {
        $redirect .= '?search=' . urlencode($current_search);
    }

    header('Location: ' . $redirect);
    exit();
}

// Fetch users based on search
if ($search !== '') {
    $results = getUserByEmail($search);
} else {
    $results = getAllUser();
}

// Load the view
require_once "../view/userlistView.php";