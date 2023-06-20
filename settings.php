<?php
    require_once("classes/AccountValidation.php");
    require_once("classes/SiteBlocks.php");
    $database = new DatabaseOperations();
    $validation = new AccountValidation();
    $siteBlocks = new SiteBlocks();
    session_start();
    if(!isset($_SESSION["userId"])){ header("location: index.php"); }
    if(isset($_POST["submit"])){
        switch ($_POST["submit"]){
            case 0:
                if(!is_uploaded_file($_FILES["userImage"]["tmp_name"])){ echo "Select an image";}
                else{
                    $image = base64_encode(file_get_contents(addslashes($_FILES["userImage"]["tmp_name"])));
                    $database->updateUser("avatar", $image);
                }
                break;
            case 1:
                if(strlen(trim($_POST["description"]))){
                    $database->updateUser("description", trim($_POST["description"]));
                }
                break;
            case 2:
                try {
                    if ($validation->validateEmail($_POST["email"]) && !$validation->emailExist($_POST["email"])) {
                        $database->updateUser("email", $_POST["email"]);
                    }
                } catch (Exception $e) { echo $e->getMessage(); }
                break;
            case 3:
                try{
                    if($validation->validatePassword($_POST["password"], $_POST["password2"])){
                        $database->updateUser("password", $_POST["password"] );
                    }
                }catch (Exception $e){ echo $e->getMessage(); }
                break;
            case 4:
                try{
                    if(!$validation->usernameExist($_POST["username"])){
                        $database->updateUser("name", $_POST["username"]);
                    }
                }catch (Exception $e){ echo $e->getMessage(); }
                break;
            case 5:
                $_SESSION["deleteFlag"] = true;
                header("location: deleteUser.php");
        }
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../projekt2/php/style.css">
        <title>User settings</title>
        <script src="https://kit.fontawesome.com/ed52e1f629.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php
            $siteBlocks->navigationBar(true);
            echo '<div class="main">';
            $siteBlocks->userProfile($_SESSION["userId"], true);
            echo '<form action="settings.php" enctype="multipart/form-data" method="post">';
            if(!isset($_POST["form"])){
                echo '<p><button type="submit" name="form" value="0">Change avatar</button> <button type="submit" name="form" value="1">Change description</button></p>
                <p><button type="submit" name="form" value="2">Change email</button> <button type="submit" name="form" value="3">Change password</button></p>
                <p><button type="submit" name="form" value="4">Change username</button> <button type="submit" name="form" value="5">Delete account</button></p>';
            }else{
                switch ($_POST["form"]){
                    case 0:
                        echo '<p><input type="file" name="userImage" value="Upload"></p>
                        <p><button type="submit" name="submit" value="0">Change</button> <button type="submit" name="submit" value="-1">Cancel</button></p>';
                        break;
                    case 1:
                        echo '<p><textarea name="description" rows="4" cols="50" placeholder="description"></textarea></p>
                        <p><button type="submit" name="submit" value="1">Change</button> <button type="submit" name="submit" value="-1">Cancel</button></p>';
                        break;
                    case 2:
                        echo '<p><input type="text" name="email" placeholder="email" required></p>
                        <p><button type="submit" name="submit" value="2">Change</button> <button type="submit" name="submit" value="-1" formnovalidate>Cancel</button></p>';
                        break;
                    case 3:
                        echo '<p><input type="password" name="password" placeholder="new password" required minlength="6"></p>
                        <p><input type="password" name="password2" placeholder="repeat password" required></p>
                        <p><button type="submit" name="submit" value="3">Change</button> <button type="submit" name="submit" value="-1" formnovalidate>Cancel</button></p>';
                        break;
                    case 4:
                        echo '<p><input type="text" name="username" placeholder="new username" required minlength="3"></p>
                        <p><button type="submit" name="submit" value="4">Change</button> <button type="submit" name="submit" value="-1" formnovalidate>Cancel</button></p>';
                        break;
                    case 5:
                        echo 'Are you sure, you want to delete your account?
                        <p><button type="submit" name="submit" value="5">Yes</button></p>
                        <p><button type="submit" name="submit" value="-1">No</button></p>';
                }
            }
            echo '</form>';
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
