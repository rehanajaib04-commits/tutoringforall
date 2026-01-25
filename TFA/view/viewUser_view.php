<!doctypehtml>
<html>
    <body>
    <form method="post" action="userlist.php">
         Search User:
                <input name="search"/>
                <input type="submit" value="Search!"/>
    </form>

        <table>
           <thead>
                    <tr>
                        <th>ID</th>
                        <th>first name</th>
                        <th>last name</th>
                        <th>number</th>
                        <th>type</th>
                    </tr>
            </thead>
            <tbody>
                 <?php foreach ($results as $user): ?> 
                        <tr>
                            <td><?= $user->user_id ?></td>
                            <td><?= $user->first_name ?></td>
                            <td><?= $user->last_name ?></td>
                            <td><?= $user->contact_number ?></td>
                            <td><?= $user->user_type ?></td>      
                        </tr>
                    <?php endforeach ?>  
            </tbody>
        </table>
    </body>
</html>