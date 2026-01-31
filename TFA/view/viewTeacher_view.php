<!doctype html>
<html>
<head>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .rating { color: #e67e22; font-weight: bold; }
    </style>
</head>
<body>
    <form method="post" action="teacherlist.php">
        Search User: <input name="search"/>
        <input type="submit" value="Search!"/>
    </form>

    <table>
        <thead>
            <tr>
                <th>Teacher Name</th>
                <th>Subject</th>
                <th>Average Rating</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $teacher): ?> 
                <tr>
                    <td>
                        <a href="teacherProfile.php?id=<?= $teacher->teacher_id ?>">
                            <?= htmlspecialchars(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')) ?: 'Unnamed Teacher' ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($teacher->teacher_type ?? 'N/A') ?></td>
                   <td class="rating">
    <?= $teacher->rating ? number_format($teacher->rating, 1) . ' / 5.0' : 'No rating' ?>
</td>
                </tr>
            <?php endforeach ?>  
        </tbody>
    </table>
</body>
</html>