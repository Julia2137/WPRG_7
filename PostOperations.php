<?php
require_once("DatabaseOperations.php");
class PostOperations{
    private $postId;
    public function __construct($postId){ $this->postId = $postId; }
    private function deleteTableAssociatedWithPost($table)
    {
        $database = new DatabaseOperations();
        $statement = $database->fetchProtectedQuery("select id from ".$table." where post_id = ?;", array($this->postId));
        if(!is_null($statement)){
            $database->protectedQuery("delete from ".$table." where post_id = ?;", array($this->postId));
        }
    }
    public function deletePost()
    {
        $database = new DatabaseOperations();
        $sql = "SELECT content_id FROM `post` WHERE id = ? ;";
        $statement = $database->fetchProtectedQuery($sql, array($this->postId));
        if(!is_null($statement[0])){
            $contentId = $statement[0];
            $sql = "select name, location from content where id = ? ;";
            $query = $database->fetchProtectedQuery($sql, array($contentId));
            unlink($query[1].$query[0]);
            rmdir($query[1]);
            $sql = "delete from content where id = ? ;";
            $database->protectedQuery($sql, array($contentId));
        }
        $this->deleteTableAssociatedWithPost("added_vote");
        $this->deleteTableAssociatedWithPost("comment");
        $sql = "delete from post where id = ? ;";
        $database->protectedQuery($sql, array($this->postId));
        header("location: index.php");
    }
    public function createComment($text)
    {
        $database = new DatabaseOperations();
        $sql = "insert into comment (description, author_id, post_id) values (?, $_SESSION[userId], ?)";
        $database->protectedQuery($sql, array($text, $this->postId));
    }
    public function editComment($commentId, $text)
    {
        $database = new DatabaseOperations();
        $sql = "update comment set description = ? where id = $commentId;";
        $database->protectedQuery($sql, array($text));
    }
    public function deleteComment($commentId)
    {
        $database = new DatabaseOperations();
        $database->Query("delete from comment where id = $commentId;");
    }
    public function addVote($isPositive)
    {
        $database = new DatabaseOperations();
        $sql = "select id from added_vote where post_id = ? and voter_id = $_SESSION[userId];";
        $statement = $database->protectedQuery($sql, array($this->postId));
        if($statement->rowCount() > 0){
            $id = $statement->fetch()[0];
            $sql = "select id from added_vote where id = $id and is_positive = $isPositive";
            if($database->query($sql)->rowCount() > 0){ $sql = "delete from added_vote where id = $id ;"; }
            else{ $sql = "update added_vote set is_positive = $isPositive where id = $id ;"; }
            $database->query($sql);
        }
        else{
            $sql = "insert into added_vote(voter_id, post_id, is_positive) values ($_SESSION[userId], ?, $isPositive);";
            $database->protectedQuery($sql, array($this->postId));
        }
        echo "<meta http-equiv='refresh' content='1'>";
    }
}
?>