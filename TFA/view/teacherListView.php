<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a Teacher - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/teacherListView.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="myprofile.php" class="navbar-brand">Tutoring For All</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link active" href="teacherlist.php">
                        <i class="bi bi-search me-1"></i>Find Teachers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="myprofile.php">
                        <i class="bi bi-person me-1"></i>My Profile
                    </a>
                </li>
                <?php if (isset($_SESSION['email_address'])): ?>
                    <li class="nav-item">
                        <span class="user-email d-none d-lg-inline"><?= htmlspecialchars($_SESSION['email_address']) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Sign Out
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="sign_in.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="page-header">
        <h1>Find a Teacher</h1>
        <p>Browse qualified tutors or search by name</p>
    </div>

    <form method="post" action="teacherlist.php" class="search-form mb-4">
        <div class="row g-2">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control form-control-lg" 
                       placeholder="Search by name..." 
                       value="<?= htmlspecialchars($_POST['search'] ?? '') ?>"/>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-dark w-100">
                    <i class="bi bi-search me-2"></i>Search
                </button>
            </div>
        </div>
    </form>

    <?php if (!empty($results)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Teacher Name</th>
                        <th>Subject</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $teacher): ?>
                        <tr>
                            <td>
                                <a href="teacherProfile.php?id=<?= urlencode($teacher->email_address) ?>" class="teacher-link">
                                    <?= htmlspecialchars((trim(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? ''))) ?: 'Unnamed Teacher') ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($teacher->teacher_type ?? 'N/A') ?></td>
                            <td class="text-end">
                                <a href="teacherProfile.php?id=<?= urlencode($teacher->email_address) ?>" class="btn btn-sm btn-outline-dark">
                                    View Profile
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-results">
            <i class="bi bi-inbox text-muted"></i>
            <p>No teachers found matching your search.</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>