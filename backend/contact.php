<?php

require_once "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../frontend/home.html");
    exit();
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($name === "" || $email === "" || $message === "") {
    die("Please fill in all fields.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}

$stmt = $pdo->prepare(
    "INSERT INTO messages (name, email, message)
     VALUES (?, ?, ?)"
);

$stmt->execute([
    $name,
    $email,
    $message
]);

echo "Message sent successfully!";