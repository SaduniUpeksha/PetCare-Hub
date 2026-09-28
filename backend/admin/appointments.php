<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireAdmin();

$message = "";


/* UPDATE STATUS */

if (isset($_POST["update_status"])) {

    $id =
        (int) $_POST["appointment_id"];

    $status =
        $_POST["status"] ?? "";


    $allowed = [
        "pending",
        "approved",
        "completed",
        "cancelled"
    ];


    if (
        in_array(
            $status,
            $allowed,
            true
        )
    ) {

        $stmt = $pdo->prepare(
            "UPDATE appointments
             SET status = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $status,
            $id
        ]);

        $message =
            "Appointment status updated.";
    }
}


/* FETCH APPOINTMENTS */

$stmt = $pdo->query(
    "SELECT
        a.id,
        a.appointment_date,
        a.status,
        u.username,
        d.name AS doctor_name,
        p.pet_name
     FROM appointments a
     JOIN users u
       ON a.user_id = u.id
     JOIN doctors d
       ON a.doctor_id = d.id
     JOIN pets p
       ON a.pet_id = p.id
     ORDER BY a.appointment_date DESC"
);

$appointments =
    $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Appointments - PetCare Hub</title>

<link
    rel="stylesheet"
    href="admin.css">

</head>


<body>


<?php require "admin_sidebar.php"; ?>


<div class="main">

    <h1 class="page-title">
        Appointment Management
    </h1>


    <?php if ($message): ?>

        <div class="message">
            <?= clean($message) ?>
        </div>

    <?php endif; ?>


    <div class="box">

        <?php if ($appointments): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>User</th>

                        <th>Pet</th>

                        <th>Doctor</th>

                        <th>Date & Time</th>

                        <th>Status</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $appointments
                    as $appointment
                ): ?>

                    <tr>

                        <td>
                            <?= (int)$appointment["id"] ?>
                        </td>

                        <td>
                            <?= clean(
                                $appointment["username"]
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $appointment["pet_name"]
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $appointment["doctor_name"]
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $appointment["appointment_date"]
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $appointment["status"]
                            ) ?>
                        </td>

                        <td>

                            <form
                                method="POST"
                                class="d-flex gap-2 align-items-center">

                                <input
                                    type="hidden"
                                    name="appointment_id"
                                    value="<?= (int)$appointment["id"] ?>">


                                <select
                                    name="status"
                                    class="status-select">

                                    <option
                                        value="pending"
                                        <?= $appointment["status"] === "pending"
                                            ? "selected"
                                            : ""
                                        ?>>

                                        Pending

                                    </option>


                                    <option
                                        value="approved"
                                        <?= $appointment["status"] === "approved"
                                            ? "selected"
                                            : ""
                                        ?>>

                                        Approved

                                    </option>


                                    <option
                                        value="completed"
                                        <?= $appointment["status"] === "completed"
                                            ? "selected"
                                            : ""
                                        ?>>

                                        Completed

                                    </option>


                                    <option
                                        value="cancelled"
                                        <?= $appointment["status"] === "cancelled"
                                            ? "selected"
                                            : ""
                                        ?>>

                                        Cancelled

                                    </option>

                                </select>


                                <button
                                    type="submit"
                                    name="update_status">

                                    Update

                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <?php else: ?>

            <div class="empty">
                No appointments found.
            </div>

        <?php endif; ?>

    </div>

</div>


</body>

</html>