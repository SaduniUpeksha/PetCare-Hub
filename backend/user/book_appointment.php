<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireLogin();

$userId = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: appointments.php");
    exit();
}

$doctorId = (int) ($_POST["doctor_id"] ?? 0);
$petId = (int) ($_POST["pet_id"] ?? 0);
$appointmentDate = $_POST["appointment_date"] ?? "";

if ($doctorId <= 0 || $petId <= 0 || $appointmentDate === "") {
    die("Please provide doctor, pet and appointment date.");
}

/* Check doctor */

$stmt = $pdo->prepare(
    "SELECT id FROM doctors WHERE id = ?"
);

$stmt->execute([$doctorId]);

if (!$stmt->fetch()) {
    die("Invalid doctor.");
}

/* Check pet belongs to logged-in user */

$stmt = $pdo->prepare(
    "SELECT id FROM pets
     WHERE id = ? AND owner_id = ?"
);

$stmt->execute([$petId, $userId]);

if (!$stmt->fetch()) {
    die("Invalid pet selection.");
}

/* Create appointment */

$stmt = $pdo->prepare(
    "INSERT INTO appointments
    (user_id, doctor_id, pet_id, appointment_date, status)
    VALUES (?, ?, ?, ?, 'pending')"
);

$stmt->execute([
    $userId,
    $doctorId,
    $petId,
    $appointmentDate
]);

header("Location: appointments.php?success=1");
exit();