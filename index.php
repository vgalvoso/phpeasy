<?php
session_start();
define('BASE_DIR',__DIR__);
require_once "Core/Config.php";
require_once "Core/Helper.php";
require_once "Core/Validator.php";
@$file = include "vendor/autoload.php";
require_once "autoload.php";

if(!view())
    api();