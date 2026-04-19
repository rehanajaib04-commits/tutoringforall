<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent & Student Registration - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="homepage.php" class="navbar-brand">Tutoring For All</a>
        <div class="navbar-nav ms-auto">
            <a href="sign_in.php" class="nav-link">Sign In</a>
        </div>
    </div>
</nav>

<div class="container py-5" style="max-width: 700px;">
    <div class="card border rounded-4 p-4 p-md-5">
        <h1 class="h3 fw-normal mb-1">Parent & Student Registration</h1>
        <p class="text-secondary mb-4">Create a parent account and link a student.</p>

        <?php if(isset($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form action="addParentStudent.php" method="POST">
            <h5 class="fw-semibold mb-3">Parent Details</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address line 1</label>
                    <input type="text" name="address_line1" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address line 2</label>
                    <input type="text" name="address_line2" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Town</label>
                    <input type="text" name="town" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">County</label>
                    <input type="text" name="county" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Post Code</label>
                    <input type="text" name="post_code" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email_address" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Security Question</label>
                    <select name="security_question" class="form-select">
                        <option value="pet">What is the name of your first pet?</option>
                        <option value="city">What city were you born in?</option>
                        <option value="school">What was the name of your first school?</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Security Answer</label>
                    <input type="text" name="security_answer" class="form-control" required>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="fw-semibold mb-3">Student Details</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Student First Name</label>
                    <input type="text" name="student_first_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Student Last Name</label>
                    <input type="text" name="student_last_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Student Contact Number</label>
                    <input type="text" name="student_contact_number" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Student Email Address</label>
                    <input type="email" name="student_email_address" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn btn-dark w-100 mt-4 py-2">Sign Up</button>
        </form>

        <div class="text-center mt-4">
            <a href="sign_in.php" class="text-secondary">Already have an account? Sign in</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>