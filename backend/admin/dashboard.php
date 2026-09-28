<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireAdmin();


$userCount = $pdo->query(
    "SELECT COUNT(*)
     FROM users
     WHERE role = 'customer'"
)->fetchColumn();


$petCount = $pdo->query(
    "SELECT COUNT(*)
     FROM pets"
)->fetchColumn();


$productCount = $pdo->query(
    "SELECT COUNT(*)
     FROM products"
)->fetchColumn();


$doctorCount = $pdo->query(
    "SELECT COUNT(*)
     FROM doctors"
)->fetchColumn();


$appointmentCount = $pdo->query(
    "SELECT COUNT(*)
     FROM appointments"
)->fetchColumn();


$messageCount = $pdo->query(
    "SELECT COUNT(*)
     FROM messages"
)->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard - PetCare Hub</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

<link
    rel="stylesheet"
    href="admin.css">

</head>


<body>


<?php require "admin_sidebar.php"; ?>


<div class="main">

    <h1 class="page-title">
        Admin Dashboard
    </h1>


    <div class="row g-4">


        <div class="col-md-4">

            <div class="card stat-card h-100">

                <div class="card-body p-4">

                    <h5>
                        Customers
                    </h5>

                    <div class="number">
                        <?= (int)$userCount ?>
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card stat-card h-100">

                <div class="card-body p-4">

                    <h5>
                        Pets
                    </h5>

                    <div class="number">
                        <?= (int)$petCount ?>
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card stat-card h-100">

                <div class="card-body p-4">

                    <h5>
                        Products
                    </h5>

                    <div class="number">
                        <?= (int)$productCount ?>
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card stat-card h-100">

                <div class="card-body p-4">

                    <h5>
                        Doctors
                    </h5>

                    <div class="number">
                        <?= (int)$doctorCount ?>
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card stat-card h-100">

                <div class="card-body p-4">

                    <h5>
                        Appointments
                    </h5>

                    <div class="number">
                        <?= (int)$appointmentCount ?>
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card stat-card h-100">

                <div class="card-body p-4">

                    <h5>
                        Messages
                    </h5>

                    <div class="number">
                        <?= (int)$messageCount ?>
                    </div>

                </div>

            </div>

        </div>


    </div>

</div>

</body>
</html>