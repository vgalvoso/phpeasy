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
<body class="full-screen col center">   
    <form method="post" action="api/addUser">
        <input type="text" name="firstName" id="" placeholder="First Name">
        <input type="text" name="lastName" id="" placeholder="Last Name">
        <input type="text" name="uname" id="" placeholder="Username">
        <input type="password" name="upass" id="" placeholder="Password">
        <input type="submit" value="Add User">
        <span id="error"></span>
    </form>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
            </tr>
        </thead>
        <tbody id="users_tbl" hx-get="api/getAllUser" hx-trigger="load">
            <!-- from GET api/getAllUser -->
        </tbody>
    </table>
</body>
</html>