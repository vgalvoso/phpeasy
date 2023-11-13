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
        <form>
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