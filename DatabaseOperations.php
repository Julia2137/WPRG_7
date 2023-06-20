<?php
///służy do wykonywania zapytań SQL, aktualizowania danych użytkownika oraz sprawdzania roli moderatora i statusu moderacji postów
class DatabaseOperations{
    private $servername = "localhost", $database = "s28580", $username = "s28580", $password = "Jul.Kasi", $connection;
    public function __construct()
    {
        try {
            $connect = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            $connect->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection = $connect;
        }
        catch (PDOException $e){ echo "Connection failed: ".$e->getMessage(); return null; }
    }
    public function query($sql){ return $this->connection->query($sql); } //używana do wykonania zapytań
    public function protectedQuery($sql, $varArray){
        $statement = $this->connection->prepare($sql);
        $statement->execute($varArray);
        return $statement;
    }
    public function fetchProtectedQuery($sql, $varArray){ return $this->protectedQuery($sql, $varArray)->fetch(); }
    public function fetchQuery($sql){ return $this->connection->query($sql)->fetch(); }

    public function updateUser($column, $sqlVar){
        $this->protectedQuery("update user set ".$column." = ? where id = $_SESSION[userId] ;", array($sqlVar));
    }
    public function isModerator($userId){
        $sql = "select id from moderator where user_id = $userId ;";
        return $this->query($sql)->rowCount() > 0;
    }
    public function isModerationPost($postId){
        $sql = "select id from moderation_post where post_id = $postId ;";
        return $this->query($sql)->rowCount() > 0;
    }
}
?>