<!doctypehtml>
<html>
    <body>
    <form method="post" action="teacherlist.php">
         Search User:
                <input name="search"/>
                <input type="submit" value="Search!"/>
    </form>

        <table>
           <thead>
                    <tr>
                        <th>ID</th>
                        <th>teacher name</th>
                    </tr>
            </thead>
            <tbody>
                 <?php foreach ($results as $teacher): ?> 
                        <tr>
                            <td><?= $teacher->teacher_id ?></td>
                            <td><?= $teacher->teacher_type ?></td>
                                  
                        </tr>
                    <?php endforeach ?>  
            </tbody>
        </table>
    </body>
</html>