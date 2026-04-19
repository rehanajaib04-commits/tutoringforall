<!DOCTYPE html>
<html>
<head>
    <title>Admin - Add New User</title>
    <link rel="stylesheet" href="/TFA/css/adminAddUser_view.css">
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
    <h2>System Administration - Add New User</h2>

    <?php if(!empty($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <?php if(!empty($success_message)): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (!isset($_GET['step']) || $_GET['step'] != '2'): ?>
        <!-- Step 1: Select User Type -->
        <div class="section-header">Step 1: Select User Type</div>
        
        <form method="get" action="">
            <input type="hidden" name="step" value="2">
            
            <div class="form-group">
                <label>User Type:</label>
                <select name="type" required>
                    <option value="">-- Select User Type --</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                    <option value="parent">Parent</option>
                    <option value="admin">System Administrator</option>
                </select>
            </div>
            
            <button type="submit">Next Step</button>
        </form>

    <?php else: ?>
        <!-- Step 2: Dynamic Form Based on Selected Type -->
        <?php $user_type = $_GET['type']; ?>
        
        <?php if (!in_array($user_type, ['teacher', 'student', 'parent', 'admin'])): ?>
            <div class="error">Invalid user type selected.</div>
            <a href="adminAddUser.php" class="back-link">← Back to Selection</a>
        <?php else: ?>
            
            <div class="section-header">Step 2: Enter <?php echo ucfirst($user_type); ?> Details</div>
            
            <form method="POST" action="?step=2&type=<?php echo htmlspecialchars($user_type); ?>">
                <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($user_type); ?>">
                
                <!-- Common Fields for All User Types -->
                <div class="form-group">
                    <label>First Name:*</label>
                    <input type="text" name="first_name" required>
                </div>
                
                <div class="form-group">
                    <label>Last Name:*</label>
                    <input type="text" name="last_name" required>
                </div>
                
                <div class="form-group">
                    <label>Contact Number:</label>
                    <input type="text" name="contact_number" placeholder="Optional">
                </div>
                
                <div class="form-group">
                    <label>Email Address:*</label>
                    <input type="email" name="email_address" required>
                </div>
                
                <div class="form-group">
                    <label>Password:*</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>Security Question:*</label>
                    <select name="security_question" required>
                        <option value="pet">What is the name of your first pet?</option>
                        <option value="city">What city were you born in?</option>
                        <option value="school">What was the name of your first school?</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Security Answer:*</label>
                    <input type="text" name="security_answer" required>
                </div>
                
                <?php if ($user_type == 'teacher'): ?>
                    <!-- Teacher Specific Fields -->
                    <div class="section-header">Professional Details</div>
                    
                    <div class="form-group">
                        <label>Teacher Type:</label>
                        <input type="text" name="teacher_type" placeholder="e.g., Full-time, Part-time, Substitute">
                    </div>
                    
                <?php elseif ($user_type == 'student'): ?>
                    <!-- Student Specific Fields -->
                    <div class="section-header">Academic Details</div>
                    
                    <div class="form-group">
                        <label>Student Type:</label>
                        <input type="text" name="student_type" placeholder="e.g., Undergraduate, Postgraduate, High School">
                    </div>
                    
                    <div class="section-header">Parent/Guardian Link</div>
                    
                    <div class="form-group">
                        <label>Link to Parent:</label>
                        <select name="parent_email">
                            <option value="">-- Select Existing Parent (Optional) --</option>
                            <?php foreach ($parents_list as $parent): ?>
                                <option value="<?php echo htmlspecialchars($parent['email_address']); ?>">
                                    <?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name'] . ' - ' . $parent['email_address']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php if (empty($parents_list)): ?>
                        <p class="orange-note">Note: No parents found in system. You can create the student now and link to a parent later.</p>
                    <?php endif; ?>
                    
                <?php elseif ($user_type == 'parent'): ?>
                    <!-- Parent Specific Fields -->
                    <div class="section-header">Parent Details</div>
                    
                    <div class="form-group">
                        <label>Parent Type:</label>
                        <input type="text" name="parent_type" placeholder="e.g., Father, Mother, Guardian">
                    </div>
                    
                    <div class="section-header">Address Information</div>
                    
                    <div class="form-group">
                        <label>Address Line 1:*</label>
                        <input type="text" name="address_line1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Address Line 2:</label>
                        <input type="text" name="address_line2">
                    </div>
                    
                    <div class="form-group">
                        <label>Town:*</label>
                        <input type="text" name="town" required>
                    </div>
                    
                    <div class="form-group">
                        <label>County:</label>
                        <input type="text" name="county">
                    </div>
                    
                    <div class="form-group">
                        <label>Post Code:*</label>
                        <input type="text" name="post_code" required>
                    </div>
                    
                <?php elseif ($user_type == 'admin'): ?>
                    <!-- Admin has no additional fields beyond common ones -->
                    <div class="section-header">Administrator Account</div>
                    <p>This will create a system administrator account with full access privileges.</p>
                <?php endif; ?>
                
                <br>
                <button type="submit">Create <?php echo ucfirst($user_type); ?> Account</button>
                <a href="adminAddUser.php" class="back-link-margin">← Start Over</a>
            </form>
            
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>
