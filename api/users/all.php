<?php
get();

$db = new DAL();
$users = new Users($db);
$userList = $users->getAll();