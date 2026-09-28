<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireAdmin();

$message = "";
$editDoctor = null;


/* DELETE */

if (isset($_POST["delete_doctor"])) {

    $id =
        (int) $_POST["doctor_id"];


    $stmt = $pdo->prepare(
        "DELETE FROM doctors
         WHERE id = ?"
    );

    $stmt->execute([
        $id
    ]);

    $message =
        "Doctor deleted successfully.";
}


/* ADD */

if (isset($_POST["add_doctor"])) {

    $name =
        trim($_POST["name"] ?? "");

    $specialization =
        trim($_POST["specialization"] ?? "");

    $experience =
        trim($_POST["experience"] ?? "");

    $email =
        trim($_POST["email"] ?? "");

    $phone =
        trim($_POST["phone"] ?? "");

    $image =
        trim($_POST["image"] ?? "");

    $availability =
        trim($_POST["availability"] ?? "");


    if (
        $name === "" ||
        $specialization === ""
    ) {

        $message =
            "Name and specialization are required.";

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO doctors
            (
                name,
                specialization,
                experience,
                email,
                phone,
                image,
                availability
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );


        $stmt->execute([
            $name,
            $specialization,
            $experience,
            $email,
            $phone,
            $image,
            $availability
        ]);


        $message =
            "Doctor added successfully.";
    }
}


/* UPDATE */

if (isset($_POST["update_doctor"])) {

    $id =
        (int) $_POST["doctor_id"];


    $name =
        trim($_POST["name"] ?? "");

    $specialization =
        trim($_POST["specialization"] ?? "");

    $experience =
        trim($_POST["experience"] ?? "");

    $email =
        trim($_POST["email"] ?? "");

    $phone =
        trim($_POST["phone"] ?? "");

    $image =
        trim($_POST["image"] ?? "");

    $availability =
        trim($_POST["availability"] ?? "");


    if (
        $name === "" ||
        $specialization === ""
    ) {

        $message =
            "Name and specialization are required.";

    } else {

        $stmt = $pdo->prepare(
            "UPDATE doctors
             SET
                name = ?,
                specialization = ?,
                experience = ?,
                email = ?,
                phone = ?,
                image = ?,
                availability = ?
             WHERE id = ?"
        );


        $stmt->execute([
            $name,
            $specialization,
            $experience,
            $email,
            $phone,
            $image,
            $availability,
            $id
        ]);


        $message =
            "Doctor updated successfully.";
    }
}


/* LOAD EDIT */

if (isset($_GET["edit"])) {

    $id =
        (int) $_GET["edit"];


    $stmt = $pdo->prepare(
        "SELECT *
         FROM doctors
         WHERE id = ?"
    );

    $stmt->execute([
        $id
    ]);


    $editDoctor =
        $stmt->fetch();
}


/* FETCH */

$stmt = $pdo->query(
    "SELECT *
     FROM doctors
     ORDER BY id DESC"
);

$doctors =
    $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Manage Doctors - PetCare Hub</title>

<link
    rel="stylesheet"
    href="admin.css">

</head>


<body>


<?php require "admin_sidebar.php"; ?>


<div class="main">

    <h1 class="page-title">
        Doctor Management
    </h1>


    <?php if ($message): ?>

        <div class="message">
            <?= clean($message) ?>
        </div>

    <?php endif; ?>


    <div class="box">


        <?php if ($editDoctor): ?>

            <h2>
                Edit Doctor
            </h2>


            <form method="POST">


                <input
                    type="hidden"
                    name="doctor_id"
                    value="<?= (int)$editDoctor["id"] ?>">


                <div class="form-grid">


                    <div class="field">

                        <label>
                            Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="<?= clean($editDoctor["name"]) ?>"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Specialization
                        </label>

                        <input
                            type="text"
                            name="specialization"
                            value="<?= clean($editDoctor["specialization"]) ?>"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Experience
                        </label>

                        <input
                            type="text"
                            name="experience"
                            value="<?= clean($editDoctor["experience"]) ?>">

                    </div>


                    <div class="field">

                        <label>
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="<?= clean($editDoctor["email"]) ?>">

                    </div>


                    <div class="field">

                        <label>
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="<?= clean($editDoctor["phone"]) ?>">

                    </div>


                    <div class="field">

                        <label>
                            Image Filename
                        </label>

                        <input
                            type="text"
                            name="image"
                            value="<?= clean($editDoctor["image"]) ?>">

                    </div>


                    <div class="field full">

                        <label>
                            Availability
                        </label>

                        <input
                            type="text"
                            name="availability"
                            value="<?= clean($editDoctor["availability"]) ?>">

                    </div>


                </div>


                <div class="buttons">

                    <button
                        type="submit"
                        name="update_doctor">

                        Update Doctor

                    </button>


                    <a
                        class="cancel-btn"
                        href="doctors.php">

                        Cancel

                    </a>

                </div>


            </form>


        <?php else: ?>


            <h2>
                Add Doctor
            </h2>


            <form method="POST">


                <div class="form-grid">


                    <div class="field">

                        <label>
                            Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            placeholder="Dr. John Silva"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Specialization
                        </label>

                        <input
                            type="text"
                            name="specialization"
                            placeholder="Veterinary Surgeon"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Experience
                        </label>

                        <input
                            type="text"
                            name="experience"
                            placeholder="8 Years">

                    </div>


                    <div class="field">

                        <label>
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            placeholder="doctor@example.com">

                    </div>


                    <div class="field">

                        <label>
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            placeholder="0712345678">

                    </div>


                    <div class="field">

                        <label>
                            Image Filename
                        </label>

                        <input
                            type="text"
                            name="image"
                            placeholder="doctor1.jpg">

                    </div>


                    <div class="field full">

                        <label>
                            Availability
                        </label>

                        <input
                            type="text"
                            name="availability"
                            placeholder="Mon - Fri, 9 AM - 4 PM">

                    </div>


                </div>


                <div class="buttons">

                    <button
                        type="submit"
                        name="add_doctor">

                        Add Doctor

                    </button>

                </div>


            </form>

        <?php endif; ?>

    </div>


    <div class="box">

        <h2>
            All Doctors
        </h2>


        <?php if ($doctors): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Specialization</th>

                        <th>Experience</th>

                        <th>Phone</th>

                        <th>Availability</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $doctors
                    as $doctor
                ): ?>

                    <tr>

                        <td>
                            <?= (int)$doctor["id"] ?>
                        </td>

                        <td>
                            <?= clean(
                                $doctor["name"]
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $doctor["specialization"]
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $doctor["experience"]
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $doctor["phone"]
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $doctor["availability"]
                            ) ?>
                        </td>

                        <td>

                            <a
                                class="edit-btn"
                                href="doctors.php?edit=<?= (int)$doctor["id"] ?>">

                                Edit

                            </a>


                            <form
                                method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Delete this doctor?');">


                                <input
                                    type="hidden"
                                    name="doctor_id"
                                    value="<?= (int)$doctor["id"] ?>">


                                <button
                                    type="submit"
                                    name="delete_doctor"
                                    class="delete-btn">

                                    Delete

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
                No doctors found.
            </div>

        <?php endif; ?>

    </div>

</div>


</body>

</html>