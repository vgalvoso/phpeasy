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
<body>    
    <div class="full-screen col center row">
        <form hx-post="api/addUser" hx-target="#users_tbl" id="add_user_form">
            <input type="text" name="fullname" id="" placeholder="Full Name">
            <input type="text" name="uname" id="" placeholder="Username">
            <input type="password" name="upass" id="" placeholder="Password">
            <input type="submit" value="Add User">
            <span id="error"></span>
        </form>
        <script>
            htmxError('#add_user_form','#error')
            htmxSuccess('#add_user_form',()=>{
                setHtml('#error','')
            })
        </script>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody id="users_tbl" hx-get="api/getAllUser" hx-trigger="load">
                <!-- from GET api/getAllUser -->
            </tbody>
        </table>
    </div>
</body>
</html>