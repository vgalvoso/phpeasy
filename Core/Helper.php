<?php
readDotEnv();
init();
function readDotEnv(){
    $envPath = BASE_DIR.'/.env';
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
        }
    }
}
function init(){
    if(isset($_SERVER['REQUEST_URI'])){
        $path = $_SERVER['REQUEST_URI'];
        //get $path except first "/"
        $path = substr($path, 1);
        //remove string from start to second "/"
        if(getenv('APP_ENV') == "development")
            $path = substr($path, strpos($path, "/") + 1);
        $method = $_SERVER['REQUEST_METHOD'];
    }else{
        $path = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
    }
    define('PATH',$path);
    define('REQUEST_METHOD',$method);
    define('BASE_URL',getenv('BASE_URL'));    
}

/**
 * Declare a php file as an HTTP POST endpoint
 */
function post(){
    if(isset($_SESSION["in_script"]) && session("in_script")){
        session("in_script",false);
        return true;
    }
    if(REQUEST_METHOD != "POST")
        notFound();
}

/**
 * Declare a php file as an HTTP GET endpoint
 */
function get(){
    if(isset($_SESSION["in_script"]) && session("in_script")){
        session("in_script",false);
        return true;
    }
    if(REQUEST_METHOD != "GET")
        notFound();
}

/**
 * Include specified view or api
 */
function to($route,$in_script=true){
    if(file_exists($route.".php"))
        include $route.".php";
    else
        include $route."/index.php";
    if($in_script)
        session("in_script",true);
    die;
}

/**
 * Extract object keys and values and store to session array
 * @param object $object The object to extract
 */
function objectToSession($object){
    // Extract keys and values from object
    $objectVars = get_object_vars($object);

    // Store keys and values as separate session variables
    foreach ($objectVars as $key => $value) {
        $_SESSION[$key] = $value;
        setcookie($key, $value, time() + 3600, '/');
    }
}

function notFound(){
    header("HTTP/1.1 404 Not Found");
    die("URL not found");
}

/**
 * Sanitize string for rendering
 */
function esc($string){
    return htmlspecialchars($string);
}

/**
 * Set content type then output content and exit script
 * @param string $content The content to output
 * @param string $contentType The content type (default application/json)
 */
function output($content,$contentType = 'application/json'){
    header("Content-Type: $contentType");
    $data = match ($contentType) {
        "application/json" => json_encode($content)
    };
    die($data);
}

/**
 * Generates a randomized alphanumeric code
 * 
 * @param int $length Length of code to be generated (default=6)
 * 
 * @return string
 */
function generateCode($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $code = '';
  
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
  
    return $code;
}

/**
 * Extract object keys and values and store to session array
 * @param object $object The object to extract
 */
function objToSession($object){
    // Extract keys and values from object
    $objectVars = get_object_vars($object);

    // Store keys and values as separate session variables
    foreach ($objectVars as $key => $value) {
        $_SESSION[$key] = $value;
    }
}

/**
 * Generate new file name and upload the file
 * @param string $uploadFile variable from input
 * @param string $uploadPath Location for upload file must add "/" to the end
 * @return boolean|string New file name if successful, false otherwise
 */
function uploadFile($uploadFile,$uploadPath){
    if(!isset($_FILES[$uploadFile]))
        return false;
    
    $file = $_FILES[$uploadFile];

    // Check for errors
    if($file['error'] !== 0)
        return false;

    // Generate a unique file name
    $new_file_name = uniqid() . '_' . $file['name'];
        
    // Upload directory
    $uploadPath = $uploadPath . $new_file_name;
        
    // Move uploaded file to the destination
    if (!move_uploaded_file($file['tmp_name'], $uploadPath))
        return false;
    return $new_file_name;
}

/**
 * Get/Set session variable
 * @param string $sessionVar Session variable name
 * @param string $value Value to set to session
 */
function session($sessionVar, $value = null){
    if($value != null)
        $_SESSION[$sessionVar] = $value;
    return $_SESSION[$sessionVar] ?? null;
}

// Converting array of objects to array of values
function objArrayToValues($objArr,$item){
    $arr = [];
        foreach($objArr as $obj){
            array_push($arr, $obj->$item);
        }
    return $arr;
}

function view(){
    if(PATH == ""){
        include "View/index.php";
        die();        
    }
    
    $checker = substr(PATH,0,4);
    if($checker == "api/")
        return false;

    $rawPath = (strpos(PATH,"?")) ? strstr(PATH, '?', true) : PATH;
    if(!file_exists("View/$rawPath.php"))
        if(!file_exists("View/$rawPath/index.php"))
            return false;
        else
            $rawPath .= "/index";

    include "View/$rawPath.php";
    die;
}

function component(){
    if(isset($_SERVER["HTTP_SEC_FETCH_MODE"]) && ($_SERVER["HTTP_SEC_FETCH_MODE"] == "navigate"))
        notFound();
}

function api(){
    if(isset($_SERVER["HTTP_SEC_FETCH_MODE"]) && ($_SERVER["HTTP_SEC_FETCH_MODE"] == "navigate"))
        notFound();
    $rawPath = substr(PATH,4);
    if(!file_exists("api/$rawPath.php"))
        if(!file_exists("api/$rawPath/index.php"))
            return false;
        else
            $rawPath .= "/index";

    if(!empty($_GET))
        extract($_GET);

    include "api/$rawPath.php";
    die;
}