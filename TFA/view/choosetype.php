<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Account Type - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/common.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="teacherlist.php" class="navbar-brand">Tutoring For All</a>
        <div class="navbar-nav ms-auto">
            <a href="sign_in.php" class="nav-link">Sign In</a>
        </div>
    </div>
</nav>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center">
        <h1 class="h2 fw-normal mb-4">Sign Up</h1>
        <p class="text-secondary mb-4">Please select your account type:</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="signup.php?type=student" class="btn btn-outline-dark btn-lg px-4 py-3">I am a Student</a>
            <a href="signup.php?type=teacher" class="btn btn-outline-dark btn-lg px-4 py-3">I am a Teacher</a>
            <a href="signup.php?type=parent" class="btn btn-outline-dark btn-lg px-4 py-3">I am a Parent</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>