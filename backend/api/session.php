<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

header("Content-Type: application/json");

$response = [
    "loggedIn" => false,
    "user_id" => null,
    "username" => null,
    "email" => null,
    "role" => null,
    "cartCount" => 0
];

if (isLoggedIn()) {

    $response["loggedIn"] = true;
    $response["user_id"] = $_SESSION["user_id"];
    $response["username"] = $_SESSION["username"];
    $response["email"] = $_SESSION["email"];
    $response["role"] = $_SESSION["role"];

    if ($_SESSION["role"] === "customer") {

        $stmt = $pdo->prepare(
            "SELECT COALESCE(SUM(quantity), 0)
             FROM cart_items
             WHERE user_id = ?"
        );

        $stmt->execute([$_SESSION["user_id"]]);

        $response["cartCount"] = (int) $stmt->fetchColumn();
    }
}

echo json_encode($response);