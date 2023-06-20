<?php
    require_once("classes/DatabaseOperations.php");
    require_once("classes/SiteBlocks.php");
    $database = new DatabaseOperations();
    $siteBlocks = new SiteBlocks();

    // zaczęcie sesji
    session_start();

    //sprawdzanie zalogowania
    if(!isset($_SESSION["userId"])){ header("location: index.php"); }

    // 0bsługa przesyłanego formularza, pobieranie ostatniego ID postu
    if(isset($_POST["submit"])){
        $sql = "select id from post order by id desc limit 1;";
        $newPostId = $database->query($sql)->rowCount() > 0 ? $database->fetchQuery($sql)[0] + 1 : 1;

        //sprawdzanie przesłanego pliku
        if(!is_uploaded_file($_FILES["file"]["tmp_name"])){
            $sql = "insert into post(title,description,author_id) values (?, ?, ?);";
            $sqlVarArray = array($_POST["title"], $_POST["description"], $_SESSION["userId"]);
            $database->protectedQuery($sql, $sqlVarArray);
        }else{ // uzytkownik przesyla plik
            $sql = "select id from content order by id desc limit 1;";
            $newContentId = $database->query($sql)->rowCount() > 0 ? $database->fetchQuery($sql)[0] + 1 : 1;
            $file = $_FILES["file"]["name"];
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            switch (strtolower($extension)){
                case 'jpg':
                case 'jpeg':
                case 'jfif':
                case 'png':
                case 'gif':
                    $contentType = 1;
                    break;
                case 'mp4':
                case 'webm':
                case 'ogg':
                    $contentType = 2;
                    break;
                default:
                    $contentType = 3;
            }
            //dodanie postu jako moderator
            $location = 'data/posts/'.$newPostId.'/';
            if(!mkdir($location)){ die('Failed to create directories'); }
            if($_FILES["file"]["error"] == UPLOAD_ERR_OK){
                $temporaryName = $_FILES["file"]["tmp_name"];
                $name = basename($_FILES["file"]["name"]);
                move_uploaded_file($temporaryName, "$location$name");
                $sql = "insert into content(name, location, content_type_id) values (?, ?, ?);";
                $sqlVarArray = array($name, $location, $contentType);
                $database->protectedQuery($sql, $sqlVarArray);
                $sql = "insert into post(title, description, content_id, author_id) values (?, ?, ?, ?);";
                $sqlVarArray = array($_POST["title"], $_POST["description"], $newContentId, $_SESSION["userId"]);
                $database->protectedQuery($sql, $sqlVarArray);
            }
        }
        // kod pobiera ID moderatora na podstawie ID użytkownika z tabeli "moderator"
        if(isset($_POST["important"])){
            $sql = "select id from moderator where user_id = $_SESSION[userId]";
            $moderatorId = $database->fetchQuery($sql)[0];
            $sql = "insert into moderation_post(post_id, moderator_id) values ($newPostId, $moderatorId);";
            $database->query($sql);
        }
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../projekt2/php/style.css">
        <title>Post creator</title>
        <script src="https://kit.fontawesome.com/ed52e1f629.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php $siteBlocks->navigationBar(true); ?>
        <div class="main">
            <form style="text-align: left" action="createPost.php" enctype="multipart/form-data" method="post">
                <p>Title: <input type="text" name="title" required></p>
                <p>Context: <textarea name="description" rows="4" cols="50"></textarea></p>
                <p><input type="file" name="file" value="Upload"></p>
                <?php if($database->isModerator($_SESSION["userId"])){ echo '<p>Post as moderator: <input type="checkbox" name="important"></p>'; } ?>
                <p><input type="submit" name="submit" value="Post"></p>
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

