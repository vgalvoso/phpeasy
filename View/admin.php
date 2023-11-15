<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="public/css/mystyle.css">
    <script src="public/js/htmx.min.js"></script>
</head>
<body>    
    <div class="full-screen col center">
        <form hx-post="api/addUser" hx-target="#result">
            <input type="text" name="username" id="">
            <input type="password" name="userPass" id="">
            <input type="text" name="firstName" id="">
            <input type="text" name="lastName" id="">
            <input type="submit" value="Add User">
        </form>
        <span id="result"></span>
    </div>
</body>
</html>