<?php

require_once "../includes/db.php";

header("Content-Type: application/json");


$stmt = $pdo->query(
    "SELECT
        id,
        name,
        specialization,
        experience,
        email,
        phone,
        image,
        availability
     FROM doctors
     ORDER BY id DESC"
);


echo json_encode(
    $stmt->fetchAll()
);