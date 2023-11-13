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
    <div class="w-75 h-100 center" id="table_container">

    </div>
</body>
</html>