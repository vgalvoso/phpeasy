<?php

namespace Core\Helper;

readDotEnv();
init();
function readDotEnv(){
    $envPath = dirname(__DIR__).'/.env';
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
    $method = "";
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
    if(REQUEST_METHOD != "GET")
        notFound();
    if(isset($_SESSION["in_script"]) && session("in_script")){
        session("in_script",false);
        return true;
    }
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
    $errData = [
        "status" => "failed",
        "code" => 404,
        "error" => "Resource not found!"
    ];
    response($errData,404);
}

/**
 * Sanitize string for rendering
 */
function esc($string){
    return htmlspecialchars($string);
}

/**
 * Enclose value of array items with specified character
 * @param array $arr|indexed Array containing items to enclose
 * @param string $char Character to be use as enclosure
 * @return array
 */
function encloseItems($arr,$char = "'"){
    foreach($arr as &$item):
        $item = $char.$item.$char;
    endforeach;
    return $arr;
}

/**
 * Set content type and status code then output content and exit script
 * @param string|array $content The content to output
 * @param int $statusCode The response status code (default 200)
 * @param string $contentType The content type (default application/json).
 *              Available content-types: [ application/json | plain/text | text/html ]
 * @return void
 */
function response(string|array $content,int $statusCode = 200,string $contentType = 'application/json',){
    header("Content-Type: $contentType");
    http_response_code($statusCode);
    $data = match ($contentType) {
        "application/json" => json_encode($content),
        "plain/text" => json_encode($content),
        "text/html" => $content
    };
    exit($data);
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
            $obj = (object)$obj;
            array_push($arr, $obj->$item);
        }
    return $arr;
}

/**
 * Check wether the path provided in request is view or an api.
 * If view includes the specified view,
 * if not, returns false.
 * 
 * @return void|false
 */
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

/**
 * State that a php view file is an SPA component.
 * This function will prevent the view file to be accessible via url,
 * can only be accessed through ajax call
 */
function component(){
    if(isset($_SERVER["HTTP_SEC_FETCH_MODE"]) && ($_SERVER["HTTP_SEC_FETCH_MODE"] == "navigate"))
        notFound();
}

/**
 * Check wether the path provided in request is an api or a view.
 * If api, includes the specified api,
 * if not, returns false.
 * 
 * @return void|false
 */
function api(){
    //get all after "api/"
    $rawPath = substr(PATH,4);
    //remove query params so raw resource uri remains eg. [users?city='caloocan'] to [users]
    $rawPath = strstr($rawPath, "?", true) ?: $rawPath;
    //check if has uri param
    $parts = explode("/",rtrim($rawPath,"/"));
    if(count($parts) > 1):
        $rawPath = $parts[0];
        //extract the uri param
        define("URI_PARAM",$parts[1]);
    endif;
    $rawPath = rtrim($rawPath,"/");
    if(!file_exists("api/$rawPath.php"))
        notFound();
    include "api/$rawPath.php";
    die;
}

/**
 * Starts a php file as a REST API
 */
function startAPI(){
    if(function_exists(strtolower(REQUEST_METHOD)))
        strtolower(REQUEST_METHOD)();
    else
        response(["status" => "failed","error" =>"Method Not Allowed!"],405);
}

/**
 * Decode JSON string from request body [file_get_contents("php://input")]
 * into associative array, exit and return 403 status code with message
 *  "Invalid json data" if fail
 * @return array
 */
function getRequestBody(){
    $data = file_get_contents("php://input");
    if(!$data = json_decode($data,true))
        response("Invalid json data");
    return $data;
}

//EOF
