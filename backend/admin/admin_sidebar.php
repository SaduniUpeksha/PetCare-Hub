<?php

$currentPage = basename($_SERVER["PHP_SELF"]);

function adminActive($page)
{
    global $currentPage;

    return $currentPage === $page
        ? "active"
        : "";
}
?>

<div class="sidebar">

    <h3>
        🐾 PetCare Hub
    </h3>


    <a
        href="dashboard.php"
        class="<?= adminActive("dashboard.php") ?>">

        📊 Dashboard

    </a>


    <a
        href="users.php"
        class="<?= adminActive("users.php") ?>">

        👤 Manage Users

    </a>


    <a
        href="pets.php"
        class="<?= adminActive("pets.php") ?>">

        🐶 Manage Pets

    </a>


    <a
        href="products.php"
        class="<?= adminActive("products.php") ?>">

        🛍️ Manage Products

    </a>


    <a
        href="categories.php"
        class="<?= adminActive("categories.php") ?>">

        📁 Categories

    </a>


    <a
        href="doctors.php"
        class="<?= adminActive("doctors.php") ?>">

        👨‍⚕️ Manage Doctors

    </a>


    <a
        href="appointments.php"
        class="<?= adminActive("appointments.php") ?>">

        📅 Appointments

    </a>


    <a
        href="messages.php"
        class="<?= adminActive("messages.php") ?>">

        📩 Messages

    </a>


    <a
        href="../auth/logout.php"
        class="logout">

        Logout

    </a>

</div>