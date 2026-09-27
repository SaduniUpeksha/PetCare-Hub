<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

header("Content-Type: application/json");

if (!isLoggedIn()) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);

    exit();
}


$userId = $_SESSION["user_id"];


$stmt = $pdo->prepare(
    "SELECT
        id,
        pet_name,
        type,
        breed,
        age,
        gender,
        price,
        description,
        image
     FROM pets
     WHERE owner_id = ?
     ORDER BY id DESC"
);

$stmt->execute([
    $userId
]);


$pets = $stmt->fetchAll();


echo json_encode($pets);