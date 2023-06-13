<?php

session_start();


$host = "szuflandia.pjwstk.edu.pl";
$username = "s28580";
$password = "Jul.Kasi";
$dbname = "s28580";

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
    die();
}
function countLikes($postID)
{
    global $pdo;
    $query = "SELECT COUNT(*) as like_count FROM likes WHERE post_id = :post_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':post_id', $postID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['like_count'];
}
// Pobranie postów z bazy danych
$postsQuery = "SELECT * FROM posts";
$postsStmt = $pdo->query($postsQuery);
$posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://kit.fontawesome.com/ed52e1f629.js" crossorigin="anonymous"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
        }

        .top-bar {
            background-color: #f7edf0;
            position: relative;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        .top-bar .left {
            display: flex;
            align-items: center;
        }

        .top-bar .right {
            display: flex;
            align-items: center;
        }

        .search-bar {
            background-color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            padding: 15px 20px;
        }

        .search-bar input {
            border: none;
            outline: none;
            background-color: transparent;
            width: 500px;
            margin-left: 5px;
            font-size: large;
        }

        .icon {
            color: #E27396;;
            margin: 0 10px;
            font-size: 200%;
        }

        .magnifying-glass {
            color: #E27396;
        }

        .line {
            width: 100%;
            height: 1px;
            background-color: black;
        }

        #sidebar {

            height: 25vh;
            background-color: #f7edf0;
            border: 1px solid black;
            border-radius: 10px;
            padding: 20px;
        }

        .sidebar textarea{
            padding: 5px;
            width: 95%;
            height: 80%;;
            padding: 5px;
            color: black;
            font-size: larger;
            background-color: #f7edf0;
            border: black;
            resize: none;
        }

        .sidebar textarea:focus{
            outline: none;
        }

        .sidebar{
            width: 20%;
        }

        #sidebardiv{
            padding: 30px;
        }

        #postbutton{
            position: relative;
            left: 80%;
            padding: 2%;
            background-color: #E27396;
            color: #fff;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            padding-left: 5%;
            padding-right: 5%;
        }

        #postbutton:hover {
            background-color: #be97c6;
        }

        #funfact{
            height: 10%;
            width: 100%;
            background-color: #f7edf0;
            border: 1px solid black;
            border-radius: 10px;
            padding: 13px;
        }

        #funfactdiv{
            padding-left: 30px;
        }

        h3{
            margin-top: 3%;
            margin-left: 3%;
        }

        li{
            margin: 20px 0;
        }

        .post {
            width: 80%;
            background-color: #f7edf0;
            border-radius: 5px;
            padding-top: 10px;
            padding-left: 10px;
            padding-right: 10px;
            top: 10%;
        }

        .post-content {
            font-size: 20px;
            line-height: 1.5;
            margin: 10px;
        }

        .post-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 10px;
            margin-right: 10px;
        }

        .icon2 {
            font-size: 20px;
            margin-right: 10px;
            color: black;
            cursor: pointer;
        }

        .likescounter{
            position: relative;
            margin-top: 19px;
            margin-right: 5px;
            font-size: 17px;
            font-weight: bolder;
        }

        #gfg {
            width: 35%;
            position: absolute;
            top: 10%;
            left: 32%;
            margin:5px;
            padding:5px;
            width: 500px;
            height: 90%;
            overflow: auto;
            text-align:justify;
        }

    </style>

</head>
<body>
<div class="top-bar">
    <div class="left">
        <i class="fas fa-cog icon"></i>
    </div>
    <div class="middle">
        <div class="search-bar">
            <i class="fas fa-search magnifying-glass"></i>
            <input type="text" placeholder="Search...">
        </div>
    </div>
    <div class="right">
        <i class="far fa-user-circle icon"></i>
        <i class="fas fa-dragon icon"></i>
        <i class="far fa-envelope icon"></i>
    </div>
</div>
<div class="line"></div>
<body>
<div id="sidebardiv">
    <div class="sidebar" id="sidebar">
        <textarea id="newpostarea" ></textarea>
        <button id="postbutton" type="button">Post</button>
    </div>
    <ul id="gfg">
    </ul>
</div>
<div id="funfactdiv" class="sidebar">
    <div id="funfact">
        <h3 style="color:#E27396">Today's Fun Fact</h3>
        <p style="margin-left: 3%;">Today is Friday.</p>
    </div>
</div>

<script>
    document.getElementById("postbutton").addEventListener("click", function() {
        var newPostContent = document.getElementById("newpostarea").value;

        // Create a new post element
        var newPost = document.createElement("div");
        newPost.className = "post";

        // Create the post content element
        var postContent = document.createElement("div");
        postContent.className = "post-content";
        postContent.textContent = newPostContent;

        // Create the post actions element
        var postActions = document.createElement("div");
        postActions.className = "post-actions";

        var commentIcon = document.createElement("i");
        commentIcon.className = "fa-regular fa-comment icon2";

        var heartIcon = document.createElement("i");
        heartIcon.className = "fa-regular fa-heart icon2";

        var likescounter = document.createElement("p");
        likescounter.textContent = "0";
        likescounter.className = "likescounter";

        // Append elements to the post actions
        postActions.appendChild(commentIcon);
        postActions.appendChild(heartIcon);
        postActions.appendChild(likescounter);

        // Append elements to the new post
        newPost.appendChild(postContent);
        newPost.appendChild(postActions);

        // Append the new post to the document body
        const node = document.createElement("li");
        node.appendChild(newPost);
        var mainpostdiv = document.getElementById("gfg");
        document.getElementById("gfg").appendChild(node);
    });

</script>

</body>
</html>
