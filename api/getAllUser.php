<?php
get();
$db = new DAL();

$users = $db->getItems("SELECT * FROM users");
foreach($users as $user):
?>
    <tr>
        <td><?=$user->username?></td>
        <td><?=$user->Name?></td>
    </tr>
<?php
endforeach;