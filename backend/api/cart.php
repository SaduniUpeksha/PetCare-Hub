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
$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {

    $stmt = $pdo->prepare(
        "SELECT
            c.id,
            c.product_id,
            c.quantity,
            p.product_name,
            p.price,
            p.image,
            p.quantity AS stock,
            (c.quantity * p.price) AS subtotal
         FROM cart_items c
         JOIN products p ON c.product_id = p.id
         WHERE c.user_id = ?
         ORDER BY c.id DESC"
    );

    $stmt->execute([$userId]);

    $items = $stmt->fetchAll();

    $total = 0;

    foreach ($items as $item) {
        $total += (float) $item["subtotal"];
    }

    echo json_encode([
        "success" => true,
        "items" => $items,
        "total" => $total
    ]);

    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$action = $data["action"] ?? "";

if ($action === "add") {

    $productId = (int) ($data["product_id"] ?? 0);

    $stmt = $pdo->prepare(
        "SELECT quantity FROM products WHERE id = ?"
    );

    $stmt->execute([$productId]);

    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode([
            "success" => false,
            "message" => "Product not found."
        ]);
        exit();
    }

    if ((int)$product["quantity"] <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Product is out of stock."
        ]);
        exit();
    }

    $stmt = $pdo->prepare(
        "SELECT quantity
         FROM cart_items
         WHERE user_id = ? AND product_id = ?"
    );

    $stmt->execute([$userId, $productId]);

    $existing = $stmt->fetch();

    if ($existing) {

        $newQuantity = (int)$existing["quantity"] + 1;

        if ($newQuantity > (int)$product["quantity"]) {
            $newQuantity = (int)$product["quantity"];
        }

        $stmt = $pdo->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE user_id = ? AND product_id = ?"
        );

        $stmt->execute([
            $newQuantity,
            $userId,
            $productId
        ]);

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO cart_items
            (user_id, product_id, quantity)
            VALUES (?, ?, 1)"
        );

        $stmt->execute([
            $userId,
            $productId
        ]);
    }

    echo json_encode([
        "success" => true,
        "message" => "Product added to cart."
    ]);

    exit();
}

if ($action === "update") {

    $productId = (int) ($data["product_id"] ?? 0);
    $quantity = (int) ($data["quantity"] ?? 1);

    if ($quantity < 1) {
        $quantity = 1;
    }

    $stmt = $pdo->prepare(
        "UPDATE cart_items
         SET quantity = ?
         WHERE user_id = ? AND product_id = ?"
    );

    $stmt->execute([
        $quantity,
        $userId,
        $productId
    ]);

    echo json_encode([
        "success" => true
    ]);

    exit();
}

if ($action === "remove") {

    $productId = (int) ($data["product_id"] ?? 0);

    $stmt = $pdo->prepare(
        "DELETE FROM cart_items
         WHERE user_id = ? AND product_id = ?"
    );

    $stmt->execute([
        $userId,
        $productId
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Item removed."
    ]);

    exit();
}

if ($action === "clear") {

    $stmt = $pdo->prepare(
        "DELETE FROM cart_items WHERE user_id = ?"
    );

    $stmt->execute([$userId]);

    echo json_encode([
        "success" => true,
        "message" => "Cart cleared."
    ]);

    exit();
}

echo json_encode([
    "success" => false,
    "message" => "Invalid action."
]);