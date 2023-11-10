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
function api($routeName,$api,$data){
    global $path;
    global $method;
    $route = $routeName;
    //for request with parameters in uri 
    //get("api/{param1,param2}","api");
    if(strpos($routeName,"{")){   
        //get routeName without parameters 
        $tempRoute = substr($routeName,0,strpos($routeName,"{")+1);
        $route = substr($tempRoute,0,strlen($tempRoute)-1);
        //get param keys
        $paramKeys = substr($routeName,strpos($routeName,"{")+1);
        $paramKeys = substr($paramKeys,0,strlen($paramKeys)-1);
        $paramKeys = explode(",",$paramKeys);

        //check if routename exists in request uri
        if(str_contains($path,$route)){
            $pos = strrpos($route,"/");
            $paramValues = substr($path,$pos+1);
            $paramValues = explode("/",$paramValues);
            if(count($paramKeys) != count($paramValues))
                notFound();
            $params = array_combine($paramKeys,$paramValues);
            $path = substr($path,0,strlen($route)-1);
            $route = substr($tempRoute,0,strlen($tempRoute)-2);
            extract($params);
        }
    }

    //for request with query params
    $path = strstr($path, "?", true) ?: $path;

    if($route != $path)
        return;
    
    if($method == 'GET')
        if(isset($_SERVER["HTTP_SEC_FETCH_MODE"]) && ($_SERVER["HTTP_SEC_FETCH_MODE"] == "navigate"))
        notFound();
    $path = $api;
    if(!is_null($data))
    extract($data);
    if(!file_exists("API/$path.php")){
        if(!file_exists("API/$path/index.php")){   
            notFound();
        }
        $path = $path."/index";
    }
    
    include "API/$path.php";
    exit();
}

function post(){
    global $method;
    if($method != "POST")
        notFound();
}

/*function get($routeName,$api){
    global $method;
    if($method != "GET") return;
    api($routeName,$api,$_GET);
}
*/

function get(){
    global $method;
    if($method != "GET")
        notFound();
}

function put($routeName,$api){
    global $method;
    if($method != "PUT") return;
    $_PUT = json_decode(file_get_contents('php://input'),true);
    api($routeName,$api,$_PUT);
}

function patch($routeName,$api){
    global $method;
    if($method != "PATCH") return;
    $_PATCH = json_decode(file_get_contents('php://input'),true);
    api($routeName,$api,$_PATCH);
}

function delete($routeName,$api){
    global $method;
    if($method != "DELETE") return;
    $_DELETE = json_decode(file_get_contents('php://input'),true);
    api($routeName,$api,$_DELETE);
}

function cli($routeName,$api){
    global $path;
    if($routeName != $path)
        return;
    include "API/$api.php";
}

function to($route){
    header("Location: $route");
    exit();
}

function notFound(){
    header("HTTP/1.1 404 Not Found");
    exit("URL not found");
}

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

    if(!empty($_GET))
        extract($_GET);

    include "View/$rawPath.php";
    exit();
}

function component(){
    if(isset($_SERVER["HTTP_SEC_FETCH_MODE"]) && ($_SERVER["HTTP_SEC_FETCH_MODE"] == "navigate"))
        notFound();
}

function newAPI(){
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