<?php
    require_once("classes/SiteBlocks.php");
    require_once("classes/PostOperations.php");
    session_start();
    if(!isset($_GET["postId"])) { header("location: index.php"); }
    if(!isset($_POST["userPanel"])) { $_POST["userPanel"] = -1 ;}

    $siteBlocks = new SiteBlocks();
    $database = new DatabaseOperations();
    $post = new PostOperations($_GET["postId"]);
    $sql = "select post.id, title, content_id, post.description, user.id, user.name, creation_time from post left join user on user.id = post.author_id where post.id = ?;";
    if($database->protectedQuery($sql, array($_GET["postId"]))->rowCount() > 0){
        $query = $database->fetchProtectedQuery($sql, array($_GET["postId"]));
        $votes = $database->fetchQuery("select countPostVotes ($query[0]);")[0];
        $isModerationPost = $database->isModerationPost($query[0]);
    }
    else{ header(" location: index.php"); }
    if(isset($_POST["comment"])){
        switch ($_POST["comment"]){
            case 0:
                $post->createComment($_POST["text"]);
                header("location: post.php?postId=$query[0]");
                break;
            case 1:
                $post->deletePost();
        }
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../projekt2/php/style.css">
        <title><?php echo $query[1] ?></title>
        <script src="https://kit.fontawesome.com/ed52e1f629.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php
            $siteBlocks->navigationBar(false);
            echo '<div class="main">';
            if(!$isModerationPost){ $siteBlocks->displayPost($votes, $query[1], $query[2], $query[3], $query[5], $query[6]); }
            else { $siteBlocks->displayModerationPost($query[1], $query[3]);}
            echo '<hr>';
            if(isset($_SESSION["userId"])) {
                echo '<form action = "" method = "post">';
                switch ($_POST["userPanel"]) {
                    case 0:
                        echo '<p><textarea name="text" rows="4" cols="50" required autofocus></textarea></p>
                        <p><button type="submit" name="comment" value="0">Comment</button> <button type="submit" name="comment" value="-1" formnovalidate>Cancel</button></p></form>';
                        break;
                    case 1:
                        $post->addVote(1);
                        break;
                    case 2:
                        $post->addVote(0);
                        break;
                    case 3:
                        echo 'Are you sure, you want to delete this post?<p><button type="submit" name="comment" value="1">Delete</button>
                        <button type="submit" name="comment" value="-1">Cancel</button></p></form>';
                        break;
                    default:
                        if (!$isModerationPost) {
                            echo '<button type="submit" name="userPanel" value="0"><i class="far fa-comment icon"></i></button>
                        <button type="submit" name="userPanel" value="1"><i class="far fa-thumbs-up icon"></i></button>
                        <button type="submit" name="userPanel" value="2"><i class="far fa-thumbs-down icon"></i></button>';
                        }
                        if ($_SESSION["userId"] == $query[4] || $database->isModerator($_SESSION["userId"])) {
                            echo ' <button type="submit" name="userPanel" value="3"><i class="far fa-hashtag icon"></i></button>';
                        }
                }
                echo '</form>';
            }
            if(!$isModerationPost){
                echo '<hr>';
                $siteBlocks->displayComments($_GET["postId"]); }
            echo '</div>';
        ?>
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
