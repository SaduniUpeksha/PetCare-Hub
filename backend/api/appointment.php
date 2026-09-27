<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

header("Content-Type: application/json");

if (!isLoggedIn()) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Please login to book an appointment."
    ]);

    exit();
}

$userId = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit();
}

$doctorId = (int) ($_POST["doctor_id"] ?? 0);
$petId = (int) ($_POST["pet_id"] ?? 0);
$date = $_POST["appointment_date"] ?? "";
$reason = trim($_POST["reason"] ?? "");

if (!$doctorId || !$petId || !$date) {

    echo json_encode([
        "success" => false,
        "message" => "Please complete all required fields."
    ]);

    exit();
}

/* Check doctor */

$stmt = $pdo->prepare(
    "SELECT id FROM doctors WHERE id = ?"
);

$stmt->execute([$doctorId]);

if (!$stmt->fetch()) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid doctor."
    ]);

    exit();
}

/* Check user's pet */

$stmt = $pdo->prepare(
    "SELECT id FROM pets
     WHERE id = ? AND owner_id = ?"
);

$stmt->execute([
    $petId,
    $userId
]);

if (!$stmt->fetch()) {

    echo json_encode([
        "success" => false,
        "message" => "Please select one of your pets."
    ]);

    exit();
}

/* Save appointment */

$stmt = $pdo->prepare(
    "INSERT INTO appointments
    (user_id, doctor_id, pet_id, appointment_date, reason, status)
    VALUES (?, ?, ?, ?, ?, 'pending')"
);

$stmt->execute([
    $userId,
    $doctorId,
    $petId,
    $date,
    $reason
]);

echo json_encode([
    "success" => true,
    "message" => "Appointment booked successfully."
]);