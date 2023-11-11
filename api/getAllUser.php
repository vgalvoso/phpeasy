<?php
get();

$db = new DAL();
$users = new Users($db);
$users = $users->getAll();
foreach($users as $user):
?>
    <tr>
        <td><?=$user->username?></td>
        <td><?=$user->first_name?></td>
        <td><?=$user->last_name?></td>
    </tr>
<?php
endforeach;