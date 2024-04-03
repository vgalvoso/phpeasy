# Welcome to PHPEasy
PHPEasy is an API-centric PHP framework. Code as close to PHP language itself rather than a framework.


## Features
1. REST API development
2. Full-stack web app development
3. File-based routing
4. Lightweight, no too much dependencies and configurations
5. Simple Database Abstraction Layer
6. Helper Functions such as input validator, code generator, upload file, etc.
7. Promotes ubt not limitted to procedural programming
8. Supports PHP 8 and above , MySQL, MSSQL and SQlite
9. Includes basic css(mystyle.css) and js(vanscript.js) utility library.

## Main Points
1. Promotes to master the PHP language itself rather than a framework.
2. Direct to the point coding, no too much abstractions.
3. Use of Data Abstraction Layer rather than ORM, focused on maximum performance without large database calls used by orms.

## Table of Contents
I. [Intro]

II. [Pre-requisites]

III. [Installation]
1. [Views]
2. [APIs]
3. [API Functions]
4. [Working with Database]
5. [Progressive]
6. [Extra]

[Intro]: #intro
[Pre-requisites]: #pre-requisites
[Installation]: #installation
[Views]: #1-views
[APIs]: #2-apis
[API Functions]: #3-api-functions
[Working with Database]: #4-working-with-database
[Progressive]: #5-progressive
[Extra]: #6-extras

## Intro
This is for someone who loves Vanilla PHP and its simplicity.
Nowadays you must follow coding standards(OOP,SOLID,DRY,etc.) and mvc frameworks to do web development
using PHP. PHP frameworks out there comes with too much files, configurations, classes and dependencies.
I made this mini framework so php developers can do web development faster while mastering and enjoying 
the PHP language itself (Yes! no need to learn so many libraries).

## Pre-requisites
1. PHP 8^.
2. Composer.

## Installation
Composer - open a terminal inside your root or htdocs folder and execute the command below.
```
composer create-project vgalvoso/phpeasy my_phpeasy
```
you can change [my_phpeasy] to any project name you want.

Now open your browser and go to http://localhost/my_phpeasy

## 1. Views
Create views inside View folder.

View routes will be automatically created based on View folder structure.

Look at examples below.
1. View file path: [View/admin/dashboard.php], the route: ["admin/dashboard"].
2. View file path: [View/login.php], the route: ["login"].

You can ommit the file name if the view file is named [index.php]:
1. View file path: [View/index.php], the route: [""].
2. View file path: [VIew/admin/index.php], the route: ["admin"].


## 1.1 View components
(SPA)Single Page Applications are composed of view components.

View components are accessible only through ajax requests.

Just call Core/Helper/component(); at the top of view file to specify it as a view component.

Example: View/admin/users_table.php
```html
<?= Core/Helper/component(); ?>
<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
        </tr>
    </thead>
    <tbody id="users_tbl">
        <tr><td>No Contents</td></tr>
    </tbody>
</table>
```

## 2. APIs
PHPEasy supports REST API.

All APIs are placed inside api folder.

API routes will automaticaly created through api folder file structure and implemented functions inside the php file named with http verbs e.g.(get(),post(),patch()).

So for example you omitted the delete() function, you can't call DELETE api/users/{id}.

Here is an example of a Users REST API.

API file path: [api/users.php]

Routes:
1. GET api/users - Get all users
2. GET api/users/{id} - Get user by id
3. POST api/users - Create new user
4. DELETE api/users/{id} - Delete a user
5. PUT api/users/{id} - Replace user
6. PATCH api/users/{id} - Update a user
```php
<?php

use Core\DAL;

use function Core\Helper\error;
use function Core\Helper\getRequestBody;
use function Core\Helper\response;
use function Core\Helper\startAPI;

startAPI();

function get(){
    $db = new DAL();
    //GET user by id
    if(defined('URI_PARAM')){
        $query = "SELECT * FROM users WHERE id = :id";
        $param = ["id" => URI_PARAM];
        if(!$user = $db->getItem($query,$param))
            response([]);
        response([$user]);
    }
    //GET All users
    $query = "SELECT * FROM users";
    $users = $db->getItems($query);
    response($users);
}

function post(){
    $db = new DAL();
    $rq = (object)getRequestBody();
    $values = [
        "username" => $rq->username,
        "firstname" => $rq->firstname,
        "lastname" => $rq->lastname,
        "usertype" => $rq->usertype,
        "password" => password_hash($rq->password,PASSWORD_BCRYPT)
    ];
    if(!$db->insert("users",$values))
        error($db->getError());
    response("New User added!");
}

function delete(){
    if(!defined('URI_PARAM'))
        error("Invalid Request! Please specify user id");
    $db = new DAL();
    $id = URI_PARAM;
    if(!$db->delete("users","id=:id",["id" => $id]))
        error($db->getError());
    response("User Deleted Successfuly!");
}

function patch(){
    if(!defined('URI_PARAM'))
        error("Invalid Request! Please specify user id");
    $db = new DAL();
    $id = URI_PARAM;
    $rq = (object)getRequestBody();
    $values = [
        "firstname" => $rq->firstname,
        "lastname" => $rq->lastname];
    $params = ["id" => $id];

    $db = new DAL();

    if(!$db->update("users","id=:id",$values,$params))
        error($db->getError());
    response("User Updated Successfuly");
}

//EOF

```

## 3. API Functions
APIs in PHPEasy encourages a procedural coding style, 

so here are the list of functions that you can use in API implementations:

## 3.3 startAPI
Initialize a PHP file as a REST API.

After calling this function you can implement http verbs as function.

Example:
```php
<?php
use function Core\Helper\startAPI;

startAPI();

function get(){
    //Handle GET request to api/users
}
```

Error response will be received if you try to request using http methods other than GET.

## 3.4 getRequestBody
Get request body and convert it into assoc array.

Example:

```php
<?php
use Core\Helper\getRequestBody;

$rq = getRequestBody();
$username = $rq["username"];
$password = $rq["password"];
//you can convert it to object for easy access
//$rq = (object)$rq;
//$username = $rq->username;
//$password = $rq->password;
```
## 3.5 validate($inputs,$validations)
Validate a key-value pair array based on validation rules.
- Return true if valid, exit and return 400 status code and error details if not.
- Use it to validate request data ($_GET,$_POST).
- `$inputs` - Associative array to be validated.
- `$validations` - Associative array containing keys that matched keys in $data and values are the validation rules.

Example:

```php
<?php
use function Core\Helper\getRequestBody;
use function Core\Validator\validate;

$rq = getRequestBody();
$dataRules = ["uname" => "required|string",
    "upass" => "required|string",
    "firstName" => "required|string",
    "lastName" => "required|string"];
validate($rq,$dataRules);
```

## 3.6 error(string|array $message)
Output response with 400 status code and error message
- `$message` - String|Array Error Message

## 3.7 response(string|array $content,int $statusCode = 200,string $contentType = 'application/json')
Set content type and status code then output content and exit script
- `$content` string|array -  The content to output
- `$statusCode` int - The response status code (default 200)
- `$contentType` string - The content type (default application/json).
 Available content-types: [ application/json | plain/text | text/html ]

## 3.8 to($route)
Include specified view
- `$route` string - View file path

Mostly used for calling SPA component

## 3.9 redirect($path="")
Redirect to specified view.

If path is not specified, redirect based on session.
- `$view` string - Path to view

## 3.10 esc($string)
Shorter syntax for htmlspecialchars()
- `$string` - String to sanitize
- Use it for HTML sanitization.

## 3.11 generateCode($length = 6)
Generate a randomized alphanumeric code
- `$length` - Length of code to be generated (default 6)

## 3.12 objectToSession($object)
Extract object keys and values and store to session array
- `$object` - The object to extract
Example:

```php
<?php
use Core\DAL;

$db = new DAL();

if(!$user = $db->getItem(1))
    invalid("User does not exist!");

objToSession($userInfo);
```

## 3.13 uploadFile($uploadFile,$uploadPath)
Generate new file name and upload the file
 - `string $uploadFile` $_FILE key
 * `string $uploadPath` Location for upload file must add "/" to the end
 * returns boolean|string New file name if successful, false otherwise

## 3.14 session($sessionVar,$value = null)
Get/Set a session variable
- `$sessionVar` - Session Key
- `$value` - Sets a session value if null

## 3.15 objArrayToValues($objArr,$item)
Convert an array of objects to indexed array containing values of specified item.
- `$objArr` - Array if ibjects to convert
- `$item` - object item to extract

## 4. Working with database
PHPEasy introduces DAL() class for database operations.
Supports MYSql, MSSql and SQLite.

Set database configurations in Config/Database.php

Below are DAL() functions

## 4.1 Initialize
```php
$db = new DAL();
```

## 4.2 insert
Executes an INSERT statement;
- `$table` - The table name to be inserted
- `$values` - Associative array containing keys that match table fields and the values to insert.
- returns boolean

Example:
```php
$values = ["username" => $uname,
    "password" => $upass,
    "firstname" => $firstName,
    "lastname" => $lastName];
$db->insert("users",$values);
```

## 4.3 update
Executes update statement
- `string $table` The table to update
- `string $condition` Conditions eg. id = :id
- `array $values` Associative array containing values to update eg .`["age" => 27]`
- `array $params` Values for conditions eg . ["id" => 1]
- return boolean

Example:
```php
$values = [
    "firstname" => $firstName,
    "lastname" => $lastName];
$params = ["id" => 1];

$db = new DAL();

$db->update("users","id=:id",$values,$params);
```

## 4.4 delete
Executes delete statement
- `string $table` The table to delete from
- `string $condition` Conditions using prepared statement eg. id = :id AND name = :name
- `array $params` Values for conditions eg. ["id" => 1,"name" => "Juan Dela Cruz"]
- return boolean

Example:
```php
$delete("users","id=:id",["id" => 1]);
```

## 4.5 getItems
Select multiple items
- `string $query` Select statement
- `array $inputs` Parameters for prepared statement default(null)
- return array|false

Example:
```php
$db = new DAL();

$sql = "SELECT * FROM users WHERE lastname = :surname";
$params = ["surname" => $lastName];

$users = $db->getItems($sql,$params);
```
## 4.6 getItem
Select single row query
- `string $query` Select statement
- `array $inputs` Parameters for prepared statement default(null)
- return object|false

Example:
```php
$db = new DAL();

$sql = "SELECT * FROM users WHERE id=:userId";
$params = ["userId" => 1];

$users = $db->getItem($sql,$params);
```

## 4.7 startTrans()
Start a database transaction.

## 4.8 commit()
Commit database transaction.
- Place this before returning a response in api.

## 4.9 rollback()
Rollback database transaction.
- Rarely used because when you exit the script without calling commit(), 
- rollback() will be automatically executed.

## 4.10 getError()
Returns Database Errors
- A very useful debugging tool.

## 4.11 lastId($field=null)
Get lastId inserted to database
- `string $field` Specify a lastId field, default null

## 4.12 getDriver()
Get the database friver that is currently used.


```
 - api/getAllUser.php
```php
<?php
$db = new DAL();
$users = new Users($db);

$usersList = $users->getAll();
```

You can use models or not depending on project requirements.

DAL class is accessible directly in api files, you can execute a query directly on api implementation without creating a Model.

## 5. Progressive
PHPEasy is progressive, you can add Models, Services if you like, just update the composer.json file if you added other directory.

## 6. Extras
See 
- js helpers: public/js/vanscript.js
- css: public/css/mystyle.css