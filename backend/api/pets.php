<?php

require_once "../includes/db.php";

header("Content-Type: application/json");

$stmt = $pdo->query(
    "SELECT
        id,
        pet_name,
        type,
        breed,
        age,
        price,
        description,
        image
     FROM pets
     ORDER BY id DESC"
);

echo json_encode($stmt->fetchAll());