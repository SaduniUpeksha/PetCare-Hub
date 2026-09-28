<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireAdmin();

$message = "";
$editPet = null;


/* DELETE PET */

if (isset($_POST["delete_pet"])) {

    $petId =
        (int) $_POST["pet_id"];


    $stmt = $pdo->prepare(
        "DELETE FROM pets
         WHERE id = ?"
    );

    $stmt->execute([
        $petId
    ]);


    $message =
        "Pet deleted successfully.";
}


/* ADD PET */

if (isset($_POST["add_pet"])) {

    $petName =
        trim($_POST["pet_name"] ?? "");

    $type =
        trim($_POST["type"] ?? "");

    $breed =
        trim($_POST["breed"] ?? "");

    $age =
        $_POST["age"] !== ""
        ? (int) $_POST["age"]
        : null;

    $price =
        $_POST["price"] ?? "";

    $description =
        trim($_POST["description"] ?? "");

    $image =
        trim($_POST["image"] ?? "");


    if (
        $petName === "" ||
        $type === "" ||
        $price === ""
    ) {

        $message =
            "Pet name, type and price are required.";

    } elseif (
        !is_numeric($price) ||
        $price < 0
    ) {

        $message =
            "Please enter a valid price.";

    } else {

        $ownerId =
            $_SESSION["user_id"];


        $stmt = $pdo->prepare(
            "INSERT INTO pets
            (
                pet_name,
                type,
                breed,
                age,
                price,
                description,
                image,
                owner_id
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );


        $stmt->execute([
            $petName,
            $type,
            $breed,
            $age,
            $price,
            $description,
            $image,
            $ownerId
        ]);


        $message =
            "Pet added successfully.";
    }
}


/* UPDATE PET */

if (isset($_POST["update_pet"])) {

    $petId =
        (int) $_POST["pet_id"];

    $petName =
        trim($_POST["pet_name"] ?? "");

    $type =
        trim($_POST["type"] ?? "");

    $breed =
        trim($_POST["breed"] ?? "");

    $age =
        $_POST["age"] !== ""
        ? (int) $_POST["age"]
        : null;

    $price =
        $_POST["price"] ?? "";

    $description =
        trim($_POST["description"] ?? "");

    $image =
        trim($_POST["image"] ?? "");


    if (
        $petName === "" ||
        $type === "" ||
        $price === ""
    ) {

        $message =
            "Pet name, type and price are required.";

    } elseif (
        !is_numeric($price) ||
        $price < 0
    ) {

        $message =
            "Please enter a valid price.";

    } else {

        $stmt = $pdo->prepare(
            "UPDATE pets
             SET
                pet_name = ?,
                type = ?,
                breed = ?,
                age = ?,
                price = ?,
                description = ?,
                image = ?
             WHERE id = ?"
        );


        $stmt->execute([
            $petName,
            $type,
            $breed,
            $age,
            $price,
            $description,
            $image,
            $petId
        ]);


        $message =
            "Pet updated successfully.";
    }
}


/* LOAD EDIT */

if (isset($_GET["edit"])) {

    $petId =
        (int) $_GET["edit"];


    $stmt = $pdo->prepare(
        "SELECT *
         FROM pets
         WHERE id = ?"
    );

    $stmt->execute([
        $petId
    ]);


    $editPet =
        $stmt->fetch();
}


/* FETCH ALL PETS */

$stmt = $pdo->query(
    "SELECT
        pets.*,
        users.username AS owner_name

     FROM pets

     LEFT JOIN users
       ON pets.owner_id = users.id

     ORDER BY pets.id DESC"
);

$pets =
    $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Manage Pets - PetCare Hub</title>

<link
    rel="stylesheet"
    href="admin.css">

</head>


<body>


<?php require "admin_sidebar.php"; ?>


<div class="main">

    <h1 class="page-title">
        Pet Management
    </h1>


    <?php if ($message): ?>

        <div class="message">
            <?= clean($message) ?>
        </div>

    <?php endif; ?>


    <div class="box">


        <?php if ($editPet): ?>


            <h2>
                Edit Pet
            </h2>


            <form method="POST">


                <input
                    type="hidden"
                    name="pet_id"
                    value="<?= (int)$editPet["id"] ?>">


                <div class="form-grid">


                    <div class="field">

                        <label>
                            Pet Name
                        </label>

                        <input
                            type="text"
                            name="pet_name"
                            value="<?= clean($editPet["pet_name"]) ?>"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Type
                        </label>

                        <input
                            type="text"
                            name="type"
                            value="<?= clean($editPet["type"]) ?>"
                            placeholder="Dog, Cat, Rabbit..."
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Breed
                        </label>

                        <input
                            type="text"
                            name="breed"
                            value="<?= clean($editPet["breed"]) ?>">

                    </div>


                    <div class="field">

                        <label>
                            Age
                        </label>

                        <input
                            type="number"
                            name="age"
                            min="0"
                            value="<?= clean($editPet["age"]) ?>">

                    </div>


                    <div class="field">

                        <label>
                            Price
                        </label>

                        <input
                            type="number"
                            name="price"
                            min="0"
                            step="0.01"
                            value="<?= clean($editPet["price"]) ?>"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Image Filename
                        </label>

                        <input
                            type="text"
                            name="image"
                            value="<?= clean($editPet["image"]) ?>"
                            placeholder="example.jpg">

                    </div>


                    <div class="field full">

                        <label>
                            Description
                        </label>

                        <textarea
                            name="description"><?= clean($editPet["description"]) ?></textarea>

                    </div>


                </div>


                <div class="buttons">

                    <button
                        type="submit"
                        name="update_pet">

                        Update Pet

                    </button>


                    <a
                        class="cancel-btn"
                        href="pets.php">

                        Cancel

                    </a>

                </div>


            </form>


        <?php else: ?>


            <h2>
                Add New Pet
            </h2>


            <form method="POST">


                <div class="form-grid">


                    <div class="field">

                        <label>
                            Pet Name
                        </label>

                        <input
                            type="text"
                            name="pet_name"
                            placeholder="e.g. Buddy"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Type
                        </label>

                        <input
                            type="text"
                            name="type"
                            placeholder="e.g. Dog"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Breed
                        </label>

                        <input
                            type="text"
                            name="breed"
                            placeholder="e.g. Golden Retriever">

                    </div>


                    <div class="field">

                        <label>
                            Age
                        </label>

                        <input
                            type="number"
                            name="age"
                            min="0">

                    </div>


                    <div class="field">

                        <label>
                            Price
                        </label>

                        <input
                            type="number"
                            name="price"
                            min="0"
                            step="0.01"
                            placeholder="e.g. 50000"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Image Filename
                        </label>

                        <input
                            type="text"
                            name="image"
                            placeholder="example.jpg">

                    </div>


                    <div class="field full">

                        <label>
                            Description
                        </label>

                        <textarea
                            name="description"
                            placeholder="Enter pet description..."></textarea>

                    </div>


                </div>


                <div class="buttons">

                    <button
                        type="submit"
                        name="add_pet">

                        Add Pet

                    </button>

                </div>


            </form>

        <?php endif; ?>


    </div>


    <div class="box">

        <h2>
            All Pets
        </h2>


        <?php if ($pets): ?>


        <div class="table-wrap">


            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Type</th>

                        <th>Breed</th>

                        <th>Age</th>

                        <th>Price</th>

                        <th>Owner</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>


                <?php foreach (
                    $pets
                    as $pet
                ): ?>


                    <tr>

                        <td>
                            <?= (int)$pet["id"] ?>
                        </td>

                        <td>
                            <?= clean(
                                $pet["pet_name"]
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $pet["type"]
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $pet["breed"]
                            ) ?>
                        </td>

                        <td>
                            <?= $pet["age"] !== null
                                ? (int)$pet["age"]
                                : "-"
                            ?>
                        </td>

                        <td>
                            Rs.
                            <?= number_format(
                                $pet["price"],
                                2
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $pet["owner_name"]
                                ?? "Unknown"
                            ) ?>
                        </td>

                        <td>

                            <a
                                class="edit-btn"
                                href="pets.php?edit=<?= (int)$pet["id"] ?>">

                                Edit

                            </a>


                            <form
                                method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Delete this pet?');">


                                <input
                                    type="hidden"
                                    name="pet_id"
                                    value="<?= (int)$pet["id"] ?>">


                                <button
                                    type="submit"
                                    name="delete_pet"
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
                No pets found.
            </div>


        <?php endif; ?>


    </div>

</div>


</body>

</html>