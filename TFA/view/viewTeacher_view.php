<!DOCTYPE html>
<html>
<head>
    <title>Find a Teacher</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            border-bottom: 2px solid #333;
        }
        .navbar .logo {
            font-size: 24px;
            font-weight: normal;
        }
        .nav-links a {
            text-decoration: none;
            color: #333;
            margin-left: 20px;
            font-size: 16px;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .nav-links a:hover { background-color: #f0f0f0; }
        .nav-links span {
            margin-left: 20px;
            font-size: 14px;
            color: #555;
        }

        .container {
            width: 80%;
            max-width: 1000px;
            margin: 40px auto;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: normal;
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }
        .search-form input[type="text"] {
            flex: 1;
            padding: 10px 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-form button {
            padding: 10px 25px;
            font-size: 16px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form button:hover { background-color: #555; }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid #333;
            font-size: 16px;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            font-size: 15px;
        }
        tr:hover td { background-color: #f9f9f9; }

        td a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        td a:hover { text-decoration: underline; }

        .no-results {
            text-align: center;
            color: #777;
            font-style: italic;
            padding: 40px 0;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo">Home</div>
    <div class="nav-links">
        <?php if (isset($_SESSION['email_address'])): ?>
            <span>Logged in as: <?= htmlspecialchars($_SESSION['email_address']) ?></span>
            <a href="logout.php">Sign Out</a>
        <?php else: ?>
            <a href="sign_in.php">Sign In</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1>Find a Teacher</h1>
    </div>

    <form method="post" action="teacherlist.php" class="search-form">
        <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($_POST['search'] ?? '') ?>"/>
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($results)): ?>
        <table>
            <thead>
                <tr>
                    <th>Teacher Name</th>
                    <th>Subject</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $teacher): ?>
                    <tr>
                        <td>
                            <a href="teacherProfile.php?id=<?= urlencode($teacher->email_address) ?>">
                                <?= htmlspecialchars((trim(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? ''))) ?: 'Unnamed Teacher') ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($teacher->teacher_type ?? 'N/A') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-results">No teachers found.</p>
    <?php endif; ?>
</div>

</body>
</html>