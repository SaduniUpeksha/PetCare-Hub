<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireLogin();

$userId = $_SESSION["user_id"];
$username = $_SESSION["username"];
$email = $_SESSION["email"];


/* PET COUNT */

$stmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM pets
     WHERE owner_id = ?"
);

$stmt->execute([
    $userId
]);

$petCount =
    $stmt->fetchColumn();


/* APPOINTMENT COUNT */

$stmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM appointments
     WHERE user_id = ?"
);

$stmt->execute([
    $userId
]);

$appointmentCount =
    $stmt->fetchColumn();


/* CART COUNT */

$stmt = $pdo->prepare(
    "SELECT COALESCE(SUM(quantity), 0)
     FROM cart_items
     WHERE user_id = ?"
);

$stmt->execute([
    $userId
]);

$cartCount =
    $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>User Dashboard - PetCare Hub</title>


<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">


<link
    rel="stylesheet"
    href="../../frontend/css/common.css">


<style>

.dashboard-card {
    border: none;
    border-radius: 16px;
    transition: 0.2s;
}

.dashboard-card:hover {
    transform: translateY(-3px);
}

.dashboard-content {
    min-height: calc(100vh - 150px);
}

</style>

</head>


<body>


<!-- SAME NAVBAR AS FRONTEND -->

<nav class="navbar navbar-expand-lg navbar-dark bg-info shadow">

<div class="container">

<a
    class="navbar-brand fw-bold"
    href="../../frontend/home.html">

    🐾 PetCare Hub

</a>


<button
    class="navbar-toggler"
    type="button"
    data-bs-toggle="collapse"
    data-bs-target="#menu">

    <span class="navbar-toggler-icon"></span>

</button>


<div
    class="collapse navbar-collapse"
    id="menu">


<ul class="navbar-nav ms-auto align-items-center">


<li class="nav-item">

<a
    class="nav-link active"
    href="../../frontend/home.html">

    Home

</a>

</li>


<li class="nav-item">

<a
    class="nav-link"
    href="../../frontend/pet.html">

    Pets

</a>

</li>


<li class="nav-item">

<a
    class="nav-link"
    href="../../frontend/products.html">

    Products

</a>

</li>


<li class="nav-item">

<a
    class="nav-link"
    href="../../frontend/doctors.html">

    Doctors

</a>

</li>


<li class="nav-item">

<a
    class="nav-link"
    href="../../frontend/appointment.html">

    Appointment

</a>

</li>


<li class="nav-item">

<a
    class="nav-link fw-bold"
    href="dashboard.php">

    Dashboard

</a>

</li>


<li class="nav-item">

<a
    class="nav-link"
    href="cart.php">

    Cart (<?= (int)$cartCount ?>)

</a>

</li>


<li class="nav-item ms-3">

<span class="nav-link fw-bold">

    👤 <?= clean($username) ?>

</span>

</li>


<li class="nav-item">

<a
    class="btn btn-dark rounded-pill px-4"
    href="../auth/logout.php">

    Logout

</a>

</li>


</ul>

</div>

</div>

</nav>


<!-- DASHBOARD -->

<div class="container py-5 dashboard-content">


<h1 class="fw-bold mb-2">

    Welcome,
    <?= clean($username) ?>! 🐾

</h1>


<p class="text-muted mb-5">

    <?= clean($email) ?>

</p>


<div class="row g-4">


<!-- PETS -->

<div class="col-md-4">

<div class="card dashboard-card shadow-sm h-100">

<div class="card-body text-center p-4">


<h4>
    My Pets
</h4>


<h1 class="text-primary">
    <?= (int)$petCount ?>
</h1>


<a
    href="pets.php"
    class="btn btn-info">

    Manage My Pets

</a>


</div>

</div>

</div>


<!-- APPOINTMENTS -->

<div class="col-md-4">

<div class="card dashboard-card shadow-sm h-100">

<div class="card-body text-center p-4">


<h4>
    My Appointments
</h4>


<h1 class="text-primary">
    <?= (int)$appointmentCount ?>
</h1>


<a
    href="appointments.php"
    class="btn btn-info">

    View Appointments

</a>


</div>

</div>

</div>


<!-- CART -->

<div class="col-md-4">

<div class="card dashboard-card shadow-sm h-100">

<div class="card-body text-center p-4">


<h4>
    My Cart
</h4>


<h1 class="text-primary">
    <?= (int)$cartCount ?>
</h1>


<a
    href="cart.php"
    class="btn btn-info">

    View Cart

</a>


</div>

</div>

</div>


</div>


<!-- QUICK ACTIONS -->

<div class="row mt-5 g-3">


<div class="col-md-3">

<a
    href="../../frontend/pet.html"
    class="btn btn-primary w-100">

    Browse Pets

</a>

</div>


<div class="col-md-3">

<a
    href="../../frontend/products.html"
    class="btn btn-primary w-100">

    Browse Products

</a>

</div>


<div class="col-md-3">

<a
    href="../../frontend/doctors.html"
    class="btn btn-primary w-100">

    Find a Doctor

</a>

</div>


<div class="col-md-3">

<a
    href="../../frontend/appointment.html"
    class="btn btn-primary w-100">

    Book Appointment

</a>

</div>


</div>


</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>