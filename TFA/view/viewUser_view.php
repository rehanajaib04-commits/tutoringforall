<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/common.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="adminDashboard.php" class="navbar-brand">Tutoring For All Admin</a>
        <div class="navbar-nav ms-auto">
            <a href="logout.php" class="nav-link">Sign Out</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <h1 class="h2 fw-normal mb-4">User Management</h1>

    <form method="post" action="userlist.php" class="row g-2 mb-4">
        <div class="col-auto">
            <input name="search" class="form-control" placeholder="Search users...">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-dark">Search</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Contact Number</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $user): ?> 
                    <tr>
                        <td><?= $user->user_id ?></td>
                        <td><?= htmlspecialchars($user->first_name) ?></td>
                        <td><?= htmlspecialchars($user->last_name) ?></td>
                        <td><?= htmlspecialchars($user->contact_number) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($user->user_type) ?></span></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>