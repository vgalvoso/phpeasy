<?php
(new DotEnv(BASE_DIR . '/.env'))->load();
$path = "";
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
define('BASE_URL',getenv('BASE_URL'));

/**
 * Declare a php file as an HTTP POST endpoint
 */
function post(){
    global $method;
    if($method != "POST")
        notFound();
}

/**
 * Declare a php file as an HTTP GET endpoint
 */
function get(){
    global $method;
    if($method != "GET")
        notFound();
}

/**
 * Submits a get request and echo its response
 * @param string $route The GET api route
 */
function to($route){
    $url = BASE_URL.$route;

    $options = [
        'http' => [
            'method' => 'GET',
            // You can add more headers if needed, such as authentication headers
            'header' => 'Content-type: application/x-www-form-urlencoded',
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        // Handle error
        echo 'Error fetching data';
    } else {
        // Process the response
        echo $response;
    }
}

function notFound(){
    header("HTTP/1.1 404 Not Found");
    exit("URL not found");
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
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
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
function objectToSession($object){
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

    // File properties
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    // Check for errors
    if($file_error !== 0)
        return false;

    // Generate a unique file name
    $new_file_name = uniqid() . '_' . $file_name;
        
    // Upload directory
    $uploadPath = $uploadPath . $new_file_name;
        
    // Move uploaded file to the destination
    if (!move_uploaded_file($file_tmp, $uploadPath))
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
    global $path;
    
    if($path == ""){
        include "View/index.php";
        exit();        
    }
    
    $checker = substr($path,0,4);
    if($checker == "api/")
        return false;

    $rawPath = (strpos($path,"?")) ? strstr($path, '?', true) : $path;
    if(!file_exists("View/$rawPath.php"))
        if(!file_exists("View/$rawPath/index.php"))
            return false;
        else
            $rawPath .= "/index";

    include "View/$rawPath.php";
    exit();
}

function component(){
    if(isset($_SERVER["HTTP_SEC_FETCH_MODE"]) && ($_SERVER["HTTP_SEC_FETCH_MODE"] == "navigate"))
        notFound();
}

function api(){
    if(isset($_SERVER["HTTP_SEC_FETCH_MODE"]) && ($_SERVER["HTTP_SEC_FETCH_MODE"] == "navigate"))
        notFound();
    global $path;
    $rawPath = substr($path,4);
    if(!file_exists("api/$rawPath.php"))
        if(!file_exists("api/$rawPath/index.php"))
            return false;
        else
            $rawPath .= "/index";

    if(!empty($_GET))
        extract($_GET);

    include "api/$rawPath.php";
    exit();
}