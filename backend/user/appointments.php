<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireLogin();

$userId = $_SESSION["user_id"];

$stmt = $pdo->prepare(
    "SELECT
        a.id,
        a.appointment_date,
        a.status,
        d.name AS doctor_name,
        d.specialization,
        p.pet_name
     FROM appointments a
     JOIN doctors d ON a.doctor_id = d.id
     JOIN pets p ON a.pet_id = p.id
     WHERE a.user_id = ?
     ORDER BY a.appointment_date DESC"
);

$stmt->execute([$userId]);

$appointments = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Appointments - PetCare Hub</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f3f6f0;
}

.container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 20px;
}

.box {
    background: white;
    padding: 25px;
    border-radius: 15px;
}

h1 {
    color: #1d4a3e;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

th {
    background: #1d4a3e;
    color: white;
}

</style>

</head>

<body>

<div class="container">

<h1>My Appointments</h1>

<a href="dashboard.php">← Dashboard</a>

<br><br>

<div class="box">

<?php if (!$appointments): ?>

<p>No appointments found.</p>

<?php else: ?>

<table>

<tr>
<th>Pet</th>
<th>Doctor</th>
<th>Specialization</th>
<th>Date</th>
<th>Status</th>
</tr>

<?php foreach ($appointments as $appointment): ?>

<tr>

<td><?= clean($appointment["pet_name"]) ?></td>

<td><?= clean($appointment["doctor_name"]) ?></td>

<td><?= clean($appointment["specialization"]) ?></td>

<td><?= clean($appointment["appointment_date"]) ?></td>

<td><?= clean($appointment["status"]) ?></td>

</tr>

<?php endforeach; ?>

</table>

<?php endif; ?>

</div>

</div>

</body>
</html>