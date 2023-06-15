<?php
// Dane o połączeniu z bazą danych
$servername = "szuflandia.pjwstk.edu.pl";
$username = "s28580";
$password = "Jul.Kasi";
$dbname = "s28580";

// Obsługa rejestracji
if (isset($_POST['name'])) {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Błąd połączenia: " . $conn->connect_error);
    }

    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Rozpoczęcie transakcji
    $conn->begin_transaction();

    try {
        $sql = "INSERT INTO users (name, surname, dob, email, username, password) VALUES ('$name', '$surname', '$dob', '$email', '$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            header("Location: a.php");
            exit();
        } else {
            throw new Exception("Wystąpił błąd podczas rejestracji.");
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
    <h1> <a href="a.php">STRONA GŁOWNA </a></h1>
    <title>Registration Page</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
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
        .container input[type="email"],
        .container input[type="password"],
        .container input[type="date"] {
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
    </style>
</head>
<body>
<div class="container">
    <h2>Registration</h2>
    <form method="post" action="rejestracja.php">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter your name">

        <label for="surname">Surname:</label>
        <input type="text" id="surname" name="surname" placeholder="Enter your surname">

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email">

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Choose a username">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Choose a password">

        <button type="submit" name="register">Register</button>
    </form>
</div>
</body>
</html>
