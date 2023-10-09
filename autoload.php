<?php
spl_autoload_register(function($className) {
	$file = "Core/".$className . '.php';
	if (file_exists($file)) {
		include $file;
	}
	$file = "Models/".$className . '.php';
	if (file_exists($file)) {
		include $file;
	}
});