<?php
session_start();

// Dane o połączeniu z bazą danych
$servername = "szuflandia.pjwstk.edu.pl";
$username = "s28580";
$password = "Jul.Kasi";
$dbname = "s28580";

// Sprawdzenie, czy użytkownik jest już zalogowany
if (isset($_SESSION['username'])) {
    header("Location: a.php");
    exit();
}

// Obsługa logowania
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Błąd połączenia: " . $conn->connect_error);
    }

    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Rozpoczęcie transakcji
    $conn->begin_transaction();

    try {
        $sql = "SELECT * FROM users WHERE username = '$inputUsername' AND password = '$inputPassword'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $_SESSION['username'] = $inputUsername;
            header("Location: a.php");
            exit();
        } else {
            $sqlInsert = "INSERT INTO users (user_id, username, password) VALUES (NULL, '$inputUsername', '$inputPassword')";
            if ($conn->query($sqlInsert) === TRUE) {
                $_SESSION['username'] = $inputUsername;
                header("Location: a.php");
                exit();
            } else {
                throw new Exception("Błąd podczas dodawania użytkownika: " . $conn->error);
            }
        }

        // Zatwierdzenie transakcji
        $conn->commit();
    } catch (Exception $e) {
        // Wystąpił błąd, cofnięcie transakcji
        $conn->rollback();
        echo $e->getMessage();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <h1> <a href="../../Desktop/projekt1/a.php"> </a></h1>
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f2f2f2;
        }

        .container {
            border-radius: 25px;
            border: 0.3px solid;
            background-color: #f7edf0;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 40px;
            max-width: 80%;
        }

        .container h2 {
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 900;
            color: #E27396;
        }

        .container label {
            display: block;
            margin-bottom: 10px;
        }

        .container input[type="text"],
        .container input[type="password"] {
            width: 90%;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }

        .container button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background-color: #E27396;
            color: #fff;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }

        .container button:hover {
            background-color: #be97c6;
        }

        .container .message {
            margin-top: 20px;
            text-align: center;
        }

        .container .message a {
            color: #ea9ab2;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Enter your username">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password">

        <button type="submit" name="login">Login</button>
    </form>
    <p class="message">Not registered yet? <a href="rejestracja.php">Create an account</a></p>
</div>
</body>
</html>
