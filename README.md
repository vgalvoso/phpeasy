# Welcome to PHPEasy
PHPEasy is a monolithic API-centric php framework.
It's goal is to enable php developers to code freely, write less and do more.

## Features
1. Monolithic API-centric architecture (non-MVC framework)
2. File-based routing
3. Made for HTMX
4. Simple Database Abstraction Layer
5. Helper Functions such as input validator, code generator, upload file, etc.
6. Procedural and OOP hybrid coding
7. Supports PHP 8 and above , MySQL, MSSQL and SQlite
8. Includes basic css and js helpers.

## Intro
For this document let's assume that the server is hosted in your local machine.
The base url is http://localhost/phpeasy

And a mysql database named (phpeasy_db)

with 1 table (users)

    id - int(11) auto increment primary,
    username - varchar(255),
    password - varchar(255),
    firstname - varchar(255),
    lastname - varchar(255)


## Installation
Manual - Download the repo and paste to htdocs or your webserver's root folder.

Composer - open a terminal inside htdocs folder and execute the command below.
```
composer create-project vgalvoso/phpeasy
```

## 1. Views
Create views inside View folder.

View routes will be automatically created based on View folder structure.
The index.php inside View folder is the entry point,
so route will be http://localhost/phpeasy.

Now create View/admin.php and paste the code below
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="public/css/mystyle.css">
    <script src="public/js/htmx.min.js"></script>
    <script src="public/js/vanscript.js"></script>
</head>
<body class="full-screen row center">
    <div class="w-25 h-100 col center">
        <form hx-post="api/addUser" h-target="#users_tbl">
            <input type="text" name="firstName" id="" placeholder="First Name">
            <input type="text" name="lastName" id="" placeholder="Last Name">
            <input type="text" name="uname" id="" placeholder="Username">
            <input type="password" name="upass" id="" placeholder="Password">
            <input type="submit" value="Add User">
        </form>
        <button hx-get="admin/users_table" hx-target="#table_container"> Show Table</button>
    </div>   
    <div class="w-75  h-100 center" id="table_container">

    </div>
</body>
</html>
```
the route will be http://localhost/phpeasy/admin


## 1.1 View components
(SPA)Single Page Applications are composed of view components,
view components are accessible only through ajax requests.

In PHPEasy just call component(); at the top of view file to specify it as a view component.

To organize views create a subdirectory and place view components inside.

Create View/admin/users_table.php and paste the code below
```html
<?= component(); ?>
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

Now go to http://localhost/phpeasy/admin and click the "Show Table" button.

## 2. APIs
Create APIs inside api folder.

API routes will automaticaly created through api folder file structure.

Let's create an api for user creation.
Create api/addUser.php and paste the code below.
```php
<?php
//Declares that this is a POST endpoint
post();

//validate request data
$dataRules = ["uname" => "required|string",
    "upass" => "required|string",
    "firstName" => "required|string",
    "lastName" => "required|string"];
validate($_POST,$dataRules);

//Filter variables to be included from request data
extract(allowedVars($_POST,$dataRules));

//Initialize the database 
$db = new DAL();

//prepare values to insert
$values = ["username" => $uname,
    "password" => $upass,
    "firstname" => $firstName,
    "lastname" => $lastName];

//Tries to insert value to users table
if(!$db->insert("users",$values))
    //return a http 403 response code and stops the script
    invalid("Can't add new user!");

//Submits a get request to api/getAllUsers
to("api/getAllUsers");
```
the route for this will be http://localhost/phpeasy/api/addUser

API implementation in PHPEasy promotes using guard clauses for more readable and shorter code.

## 3. API Functions
APIs in PHPEasy encourages a procedural coding style, 

so here are the list of functions that you can use in API implementations:

## 3.1 get()
Declare a php file as HTTP GET endpoint.
- Can't be accessed through Sec-Fetch-Mode('navigate')
- Place it at the top of php file.

Example:
```php
<?php
get();
```

## 3.2 post()
Declare a php file as HTTP POST endpoint.
- Can't be accessed through Sec-Fetch-Mode('navigate')
- Place it at the top of php file.

Example:
```php
<?php
post();
```

## 3.3 validate($inputs,$validations)
Validate a key-value pair array based on validation rules.
- Returns true if valid, Echo errors if invalid and exits the script.
- Use it to validate request data ($_GET,$_POST).
- `$inputs` - Associative array to be validated.
- `$validations` - Associative array containing keys that matched keys in $data and values are the validation rules.

Example:

api/addUser.php
```php
<?php
post();
//Form data from View/admin.php
$dataRules = ["uname" => "required|string",
    "upass" => "required|string",
    "firstName" => "required|string",
    "lastName" => "required|string"];
validate($_POST,$dataRules);
```

## 3.4 allowedVars($inputs,$rules)
Filter an associative array based on $rules(same as $dataRules in validate()) and place it in 1 array.
- returns - Associative Array
- `$inputs` - Associative array to filter ($_GET/$_POST)
- `$rules` - Associative array

## 3.5 invalid($message)
Return HTTP 403 response code, message and exits the script.
- `$message` - String for response

## 3.6 to($getEndpoint)
Submits a GET request and echo its response
- `$getEndpoint` - GET API endpoint

## 3.7 esc($string)
Shorter syntax for htmlspecialchars()
- `$string` - String to sanitize
- Use it for echoing HTML sanitazion.

## 3.8 output($content,$contentType = 'application/json')
Set content type, output the content and exit script
- `$content` - String to output
- `$contentType` - Sets content-type header defaults application/json

## 3.9 generateCode($length = 6)
Generate a randomized alphanumeric code
- `$length` - Length of code to be generated (default 6)

## 3.10 objectToSession($object)
Extract object keys and values and store to session array
- `$object` - The object to extract
Example:

```php
<?php
$db = new DAL();
$user = new User($db);

if(!$userInfo = $user->getDetails($userId))
    invalid("User does not exist!");

objToSession($userInfo);
```

## 3.11 uploadFile($uploadFile,$uploadPath)
Generate new file name and upload the file
 - `string $uploadFile` $_FILE key
 * `string $uploadPath` Location for upload file must add "/" to the end
 * returns boolean|string New file name if successful, false otherwise

## 3.12 session($sessionVar,$value = null)
Get/Set a session variable
- `$sessionVar` - Session Key
- `$value` - Sets a session value if null

## 3.13 objArrayToValues($objArr,$item)
Convert an array of objects to indexed array containing values of specified item.
- `$objArr` - Array if ibjects to convert
- `$item` - object item to extract

## 3.14 invalid($message)
Returns a 403 HTTP response code, outputs `$message` and exit the script.

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

$db->update("users","id=:id",$values,$params);
```

## 4.4 delete
## 4.5 getItems
## 4.6 getItem
## 4.7 startTrans
## 4.8 commit
## 4.9 rollback
## 4.10 getError
## 4.11 lastId
## 4.12 getDriver

# 5. Model
Create models inside Models folder