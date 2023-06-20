<?php
    session_start();
    require_once("classes/SiteBlocks.php");
    $siteBlocks = new SiteBlocks();
    $database = new DatabaseOperations();
    if(isset($_POST["submit"])){
        $sql = "select id from user where (name = ? or email = ?) and password = ?;" ;
        $sqlVarArray = array($_POST["login"], $_POST["login"], $_POST["password"]);
        $statement = $database->protectedQuery($sql, $sqlVarArray);
        if($statement->rowCount() > 0){
            $_SESSION["userId"] = $statement->fetch()[0];
            header("location: index.php");
        }
        else{
            echo "<p>Wrong login or password</p>";
        }
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../projekt2/php/style.css">
        <title>Login form</title>
        <script src="https://kit.fontawesome.com/ed52e1f629.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php $siteBlocks->logRegNavigationBar(true); ?>
        <div class="main">
            <form action="login.php" method="post">
                <p>Username or E-mail: <input type="text" name="login" required></p>
                <p>Password: <input type="password" name="password" required></p>
                <p><input type="submit" name="submit" value="Login"></p><br>
                <a id="register" href="register.php">I don't have account</a>
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
