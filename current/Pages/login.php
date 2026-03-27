<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
    
    <title>F1 Pick'em</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"> <!-- Include Normalize.css via CDN -->
    <link rel="stylesheet" href="../Styles/common.css?v=1">
    <link rel="stylesheet" href="../Styles/loginStyle.css?v=1">
    <link rel="icon" type="image/x-icon" href="../Images/F1Pickem_logo.ico">
    
    <?php require_once "../Methods/Login.php"; ?>
</head>

<body>
    <div id="page-name">F1 Pick'em</div>
    
    <div class="plate-1">
        <form action="../Methods/Login.php" method="post">
            <table style="margin: auto; border-spacing: 8px;">
                <tr>
                    <td><label for="name" class="label">Username:<label></td>
                    <td><input type="text" class="input" name="usernameLogin"></td>
                </tr>
                <tr>
                    <td><label for="pass" class="label">Password:<label></td>
                    <td><input type="password" class="input" name="passwordLogin"></td>
                </tr>
            </table>
            <?php
                $error = $_GET["error"] ?? "";
                if ($error === "loginMismatch")
                    echo "<p id='error'>*Invalid username or password!*</p>";
                else if ($error === "sessionExpired")
                    echo "<p id='error'>*You have been logged out!*</p>";
            ?>
            <input type="submit" id="login1" value="Login">
        </form>
    </div>
    
    <p id="info" style="text-align: center;">*contact 940-600-9250 for login information*</p>
</body>
</html>