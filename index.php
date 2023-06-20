<?php
    require_once("classes\SiteBlocks.php");
    session_start();
    $database = new DatabaseOperations();
    $siteBlocks = new SiteBlocks();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../projekt2/php/style.css">
        <title>One of internet's forum</title>
        <script src="https://kit.fontawesome.com/ed52e1f629.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php
            $siteBlocks->navigationBar(false);
            echo '<div class = "main">
            <h1>Posts from Admins</h1>';
            $siteBlocks->moderationPosts();
            echo '<hr>
            <h1>Users Posts</h1>';
            $siteBlocks->posts(null);
        ?>
        <h3>End of posts</h3>
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
