<?php

require_once "../includes/db.php";

header("Content-Type: application/json");

$stmt = $pdo->query(
    "SELECT
        id,
        product_name,
        category,
        price,
        quantity,
        image
     FROM products
     ORDER BY id DESC"
);

echo json_encode($stmt->fetchAll());