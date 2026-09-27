<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $message = "Please enter username and password.";

    } else {

        $stmt = $pdo->prepare(
            "SELECT id, username, email, password, role
             FROM users
             WHERE username = ?"
        );

        $stmt->execute([$username]);

        $user = $stmt->fetch();

        if (!$user) {

            $message = "Username not found.";

        } elseif (!password_verify($password, $user["password"])) {

            $message = "Incorrect password.";

        } else {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === "admin") {

                header("Location: ../admin/dashboard.php");

            } else {

                header("Location: ../../frontend/home.html");
            }

            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Login - PetCare Hub</title>

</head>

<body>

<h2><?= htmlspecialchars($message) ?></h2>

<form action="login.php" method="POST">

<label>Username:</label>

<input
type="text"
name="username"
required>

<br><br>

<label>Password:</label>

<input
type="password"
name="password"
required>

<br><br>

<button type="submit">
Login
</button>

</form>

</body>

</html>