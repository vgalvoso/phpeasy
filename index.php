<?php

define('BASE_DIR',__DIR__);

include "vendor/autoload.php";

use function Core\Helper\api;
use function Core\Helper\view;

if(!view())
    api();

//EOF
