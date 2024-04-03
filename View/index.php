<?=Core\Helper\redirect()?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="public/css/mystyle.css">
    <script src="public/js/htmx.min.js"></script>
    <script src="public/js/vanscript.js"></script>
</head>
<body>
    <div class="full-screen center">
        <form class="bg-white shadow pad-big w-25 mobile-grow mar round"
            method="post"
            action="api/login">
            <h1 class="text-header primary pad">Login</h1>
            <input type="text" class="pad" name="username" placeholder="Username" id="">
            <input type="password" class="pad" name="password" placeholder="Password" id="">
            <input type="submit" class="pad" value="Login">
        </form>
    </div>
</body>
</html>
