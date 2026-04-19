<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    
 
<div class="container mt-5">
       <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="homepage.php" class="navbar-brand">Tutoring For All Admin</a>
        <div class="navbar-nav ms-auto">
            <a href="logout.php" class="nav-link">Sign Out</a>
        </div>
    </div>
</nav>
    <h2>User List</h2>
    
    <!-- Flash Messages -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= htmlspecialchars($message_type) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-9 d-flex justify-content-between mb-3">
            <form method="post" class="d-flex w-100">
                <input type="text" class="form-control" name="search" placeholder="Search by email address" value="<?= htmlspecialchars($_POST['search'] ?? ($_GET['search'] ?? '')) ?>">
                <button type="submit" class="btn btn-dark search-button ms-2">Search</button>
            </form>
        </div>
    </div>
    <div class="card border results-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Email Address</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Contact Number</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($results)): ?>
                        <?php foreach ($results as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user->email_address) ?></td>
                                <td><?= htmlspecialchars($user->first_name) ?></td>
                                <td><?= htmlspecialchars($user->last_name) ?></td>
                                <td><?= htmlspecialchars($user->contact_number) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($user->user_type) ?></span></td>
                                <td>
                                    <a href="userDetails.php?email=<?= urlencode($user->email_address) ?>" class="btn btn-sm btn-outline-primary me-1">View</a>
                                    <?php if (strtolower($user->user_type) === 'admin'): ?>
                                        <span class="badge bg-danger">Cannot delete</span>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-dark me-1" data-bs-toggle="modal" data-bs-target="#passwordModal<?= md5($user->email_address) ?>">
                                            <i class="bi bi-key me-1"></i>Change Password
                                        </button>
                                        <form method="post" action="userlist.php<?= !empty($search) ? '?search=' . urlencode($search) : '' ?>" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" style="display:inline;">
                                            <input type="hidden" name="delete_user" value="1">
                                            <input type="hidden" name="delete_user_email" value="<?= htmlspecialchars($user->email_address) ?>">
                                            <input type="hidden" name="current_search" value="<?= htmlspecialchars($search) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            
                            <!-- Password Change Modal -->
                            <?php if (strtolower($user->user_type) !== 'admin'): ?>
                                <div class="modal fade" id="passwordModal<?= md5($user->email_address) ?>" tabindex="-1" aria-labelledby="passwordModalLabel<?= md5($user->email_address) ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="passwordModalLabel<?= md5($user->email_address) ?>">
                                                    <i class="bi bi-shield-lock me-2"></i>Change Password
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="userlist.php<?= !empty($search) ? '?search=' . urlencode($search) : '' ?>">
                                                <div class="modal-body">
                                                    <input type="hidden" name="change_password" value="1">
                                                    <input type="hidden" name="target_user_email" value="<?= htmlspecialchars($user->email_address) ?>">
                                                    <input type="hidden" name="current_search" value="<?= htmlspecialchars($search) ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">User</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($user->email_address) ?>" disabled>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">New Password</label>
                                                        <input type="password" name="new_password" class="form-control" placeholder="Enter new password" autocomplete="new-password" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Confirm Password</label>
                                                        <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password" autocomplete="new-password" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-dark">
                                                        <i class="bi bi-key me-2"></i>Update Password
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="systemAdminstrator.php" class="btn btn-outline-secondary">← Add New User</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>