<?php
  session_start();
  define('BASE_DIR',__DIR__);
  $file = "vendor/autoload.php";
  if (file_exists($file)) {
		include $file;
	}
  require_once "autoload.php";
  require_once "Config/App.php";
  require_once "Config/Constants.php";
  require_once "Core/Helper.php";
  require_once "Core/Validator.php";
  require_once "routes/api.php";
  require_once "routes/web.php";
?>