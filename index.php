<?php
define('BASE_DIR',__DIR__);
require_once "Config/App.php";
require_once "Config/Constants.php";
require_once "Config/Database.php";
require_once "Core/Helper.php";
require_once "Core/Validator.php";
$file = "vendor/autoload.php";
if (file_exists($file)) {
    include $file;
}
require_once "autoload.php";

if(!view())
    api();