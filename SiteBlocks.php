<?php
require_once("DatabaseOperations.php");
require_once("PostOperations.php");
class SiteBlocks{
    //wyświetla logo strony, przycisk logowania/odpowiednie opcje użytkownika zależnie od tego, czy użytkownik jest zalogowany,
    public function navigationBar($noAdditionalPages){
        $database = new DatabaseOperations();
        echo '<div class="navbar"><a href="index.php"><i class="fas fa-dragon icon"></i></a>';
        if(!isset($_SESSION['userId'])){ echo ' <a href="login.php"><i class="far fa-user-circle icon"></i></a>'; }
        else{
            $sql = "select name, avatar from user where id = $_SESSION[userId]";
            $row = $database->fetchQuery($sql);
            if(!$noAdditionalPages){ echo ' <a href="createPost.php"><i class="far fa-envelope icon"></i></a> <a href="settings.php"><i class="fas fa-cog icon"></i></a> <a href="logout.php"><i class="fas fa-share icon"></i></a>'; }
            echo '<div id="userAvatar"><a href="userProfile.php?username='.$row[0].'">'.$row[0].' <img class="miniAvatar" src="data:image/jpeg;base64,'.$row['avatar'].'"/></a></div>'; }
        echo '</div>';
    }
//Wyświetla logo strony i opcję logowania, jeśli $onLogin jest prawdziwe
    public function logRegNavigationBar($onLogin){
        echo '<div class="navbar">
        <a href="index.php"><i class="fas fa-dragon icon"></i></a>';
        if($onLogin){  }
        else{ echo ' <a href="login.php"><i class="far fa-user-circle icon"></i></a>';}
        echo '</div>';
    }
//Wyświetla profil użytkownika na podstawie podanego identyfikatora
    public function userProfile($nameOrId, $isId){
        $database = new DatabaseOperations();
        $sql = "select id, name, email, join_date, avatar, description from user where ";
        $sql .= $isId ? "id = ? ;" : "name = ? ;";
        $row = $database->fetchProtectedQuery($sql, array($nameOrId));
        $votes = $database->fetchQuery("select countUserVotes($row[0])")[0];
        echo '<div class="profile"><p>'.$row["name"].' ( '.$row["email"].' ) Rank '.$votes.'</p>
        <img class="avatar" src="data:image/jpeg;base64,'.$row['avatar'].'"/>
        <p>'.$row["description"].'</p>
        <p>Member for '.$this->displayElapsedTime(strtotime($row["join_date"])).'</p></div>';
    }

//"2 dni temu", "1 godzina temu"
    public function displayElapsedTime($time){
        $time = time() - $time;
        $time = ($time < 1)? 1 : $time;
        $tokens = array( 31536000 => 'year', 2592000 => 'month', 604800 => 'week', 86400 => 'day', 3600 => 'hour', 60 => 'minute', 1 => 'second' );
        foreach ($tokens as $unit => $text){
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits." ".$text.(($numberOfUnits>1)?"s":"");
        }
    }
    //yświetla tytuł postu w sekcji moderacji
    private function displayModerationPostTitle($id, $title){ echo '<div class="AdminPost">
        <h2><a class="posttopic" href="post.php?postId='.$id.'">'.$title.'</a></h2></div><br>'; }
    public function moderationPosts(){
        $database = new DatabaseOperations();
        $sql = "select post.id, title from post inner join moderation_post on post.id = moderation_post.post_id order by creation_time desc;";
        $query = $database->query($sql);
        if($query->rowCount() > 0){
            echo '<div class="adminPosts">';
            foreach ($query as $row){
                $this->displayModerationPostTitle($row[0], $row[1]);
            }
            echo '</div>';
        }
    }

    private function displayPostTitle($votes, $id, $title, $author, $creationTime){
        echo '<div class="mainPost"><h2><a class="posttopic" href="post.php?postId='.$id.'">'.$title.'</a></h2>Votes '.$votes.', Posted';
        if(!is_null($author)) echo ' by <a class="links" href="userProfile.php?username='.$author.'">'.$author.'</a>';
        echo ' '.$this->displayElapsedTime(strtotime($creationTime)).' ago</div>';
    }

    public function posts($username){
        $database = new DatabaseOperations();
        $sqlVarArray = null;
        $sql = "select post.id, title, user.name, creation_time from post left join user on user.id = post.author_id";
        if(!is_null($username)){
            $sql .= " where user.name = ?";
            $sqlVarArray = array($username);
        }
        $sql .= " order by creation_time desc;";
        $query = $database->protectedQuery($sql, $sqlVarArray);
        if($query->rowCount() > 0){
            echo '<div class="posts">';
            foreach ($query as $row){
                $sql = "select id from moderation_post where post_id = $row[id]";
                if($database->query($sql)->rowCount() > 0){ continue; }
                $sql = "select countPostVotes ($row[id])";
                $votes = $database->fetchQuery($sql)[0];
                $this->displayPostTitle($votes, $row["id"], $row["title"], (is_null($username)) ? $row["name"] : null , $row["creation_time"]);
            }
            echo '</div>';
        }
    }
    public function displayModerationPost($title, $description){
        $database = new DatabaseOperations();
        echo '<div class="post"><h2>'.$title.'</h2><p>';
        if(!is_null($description) && $description != ""){ echo '<p>'.$description.'</p>'; }
    }
    //Wyświetla pełny post na stronie
    public function displayPost($votes, $title, $contentId, $description, $author, $creationTime){
        $database = new DatabaseOperations();
        echo '<div class="post"><h1> '.$title.'</h1><p>Votes '.$votes.', Posted ';
        if(!is_null($author)){ echo 'by <a href="userProfile.php?username='.$author.'">'.$author.'</a> '; }
        echo $this->displayElapsedTime(strtotime($creationTime)).' ago</p>';
        if(!is_null($contentId)){
            $sql = "select name, location, type from content inner join content_type on content.content_type_id = content_type.id where content.id = ? ;";
            $query = $database->fetchProtectedQuery($sql, array($contentId));
            echo '<div class="content">';
            switch ($query[2]){
                case "image":
                    echo '<img src="'.$query[1].$query[0].'"/>';
                    break;
                case "video":
                    echo '<video width="320" height="240" controls>
                    <source src="'.$query[1].$query[0].'" type="video/mp4">
                    <source src="'.$query[1].$query[0].'" type="video/webm">
                    <source src="'.$query[1].$query[0].'" type="video/ogg">
                    Your browser does not support the video tag.</video>';
                    break;
                case "file":
                    echo '<a href="'.$query[1].$query[0].'" download>'.$query[0].'</a>';
            }
            echo '</div>';
        }
        if(!is_null($description) && $description != ""){ echo '<h3>'.$description.'</h3>'; }
    }
//Wyświetla komentarz wraz z informacjami o autorze, czasem utworzenia it
    private function comment($commentId, $description, $userId, $author, $creationTime, $postId){
        $post = new PostOperations($postId);
        $database = new DatabaseOperations();
        echo '<div class="comment"><p>Commented ';
        if(!is_null($author)){ echo ' by <a href="userProfile.php?username='.$author.'">'.$author.'</a> '; }
        echo $this->displayElapsedTime(strtotime($creationTime)).' ago</p><h3>'.$description.'</h3></div>';
        if(isset($_SESSION["userId"])){
            if ($_SESSION["userId"] == $userId || $database->isModerator($_SESSION["userId"])) {
                if (!isset($_POST["commentPanel"])) { $_POST["commentPanel"] = -1; }
                echo '<form action = "" method = "post">';
                switch ($_POST["commentPanel"]) {
                    case 0:
                        if(isset($_POST["formId"]) && $_POST["formId"] == $commentId){
                            echo '<input type="hidden" name="formId" value="'.$commentId.'">
                            <p><textarea name="commentText" rows="4" cols="50" required autofocus></textarea></p>
                            <p><button type="submit" name="commentSubmit" value="0"><i class="far fa-star icon"></i></button><button type="submit" name="commentSubmit" value="-1" formnovalidate><i class="far fa-share-from-square icon"></i></button></p>';
                        }
                        break;
                    case 1:
                        if(isset($_POST["formId"]) && $_POST["formId"] == $commentId){
                            echo 'Are you sure, you want to delete this comment?<input type="hidden" name="formId" value="'.$commentId.'">
                            <p><button type="submit" name="commentSubmit" value="1"><i class="far fa-hashtag icon"></i></button>
                            <button type="submit" name="commentSubmit" value="-1"><i class="far fa-share-from-square icon"></i></button></p>';
                        }
                        break;
                    default:
                        echo '<input type="hidden" name="formId" value="'.$commentId.'">
                        <p><button type="submit" name="commentPanel" value="0"><i class="far fa-star icon"></i></button>
                        <button type="submit" name="commentPanel" value="1"><i class="far fa-hashtag icon"></i></button></p>';
                }
                echo '</form>';
                if (isset($_POST["commentSubmit"]) && $_POST["formId"] == $commentId) {
                    switch ($_POST["commentSubmit"]) {
                        case 0:
                            $post->editComment($commentId, $_POST["commentText"]);
                            break;
                        case 1:
                            $post->deleteComment($commentId);
                            break;
                    }
                    echo "<meta http-equiv='refresh' content='1'>";
                }
            }
        }
    }
    public function displayComments($postId){
        $database = new DatabaseOperations();
        $sql = "select comment.id, comment.description, user.id, name, creation_time from comment left join user on user.id = comment.author_id where post_id = ?;";
        $query = $database->protectedQuery($sql, array($postId));
        if($query->rowCount() > 0){
            echo '<div class="comments">';
            foreach ($query as $row){ $this->comment($row[0], $row[1], $row[2], $row[3], $row[4], $postId); }
            echo '</div>';
        }
    }
}
?>