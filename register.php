<?php
    require_once("classes/AccountValidation.php");
    require_once("classes/SiteBlocks.php");
    $accountValidation = new AccountValidation();
    $siteBlocks = new SiteBlocks();
    $database = new DatabaseOperations();
    if(isset($_POST["submit"])){
        $passed = false;
        try{
            $accountValidation->usernameExist($_POST["username"]);
            $accountValidation->validateEmail($_POST["email"]);
            $accountValidation->validatePassword($_POST["password"], $_POST["password2"]);
            $passed = true;
        }
        catch (Exception $e){
            echo $e->getMessage(), "\n";
        }
        if($passed){
            $defaultAvatar = base64_encode(file_get_contents('data/avatars/default/-1.jpg'));
            $sql = "insert into user(name,password,email,avatar) values (?, ?, ?, ?)";
            $sqlVarArray = array($_POST["username"], $_POST["password"], $_POST["email"], $defaultAvatar);
            $database->protectedQuery($sql, $sqlVarArray);
            $sql = "select id from user where name = ?  and password = ?;" ;
            $sqlVarArray = array($_POST["username"], $_POST["password"]);
            $statement = $database->protectedQuery($sql, $sqlVarArray);
            if($statement->rowCount() > 0){
                session_start();
                $_SESSION["userId"] = $statement->fetch()[0];
                header("location: index.php");
            }
        }
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../projekt2/php/style.css">
        <title>Registration form</title>
        <script src="https://kit.fontawesome.com/ed52e1f629.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php $siteBlocks->logRegNavigationBar(false); ?>
        <div class="main">
            <form action="register.php" method="post">
                <p>Username: <input type="text" name="username" required minlength="3"></p>
                <p>E-mail: <input type="text" name="email" required></p>
                <p>Password: <input type="password" name="password" required minlength="6"></p>
                <p>Confirm password: <input type="password" name="password2" required></p>
                <p><input type="submit" name="submit" value="Register"></p>
            </form>
        </div>
        <h7><p id="zegar">Aktualna godzina: <span id="current-time"></span></p></h7>
        <script>
    function updateClock() {
        var now = new Date();
        var hour = now.getHours();
        var minute = now.getMinutes();
        var second = now.getSeconds();

        // Dodawanie 0 przed jednocyfrowymi liczbami
        hour = (hour < 10) ? "0" + hour : hour;
        minute = (minute < 10) ? "0" + minute : minute;
        second = (second < 10) ? "0" + second : second;

        var time = hour + ":" + minute + ":" + second;

        // Aktualizacja elementu HTML z godziną
        document.getElementById("current-time").innerHTML = time;

        // Wywołanie funkcji co 1 sekundę
        setTimeout(updateClock, 1000);
    }
    updateClock();
    </script>
    </body>
</html>
