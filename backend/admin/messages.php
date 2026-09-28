<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireAdmin();

$message = "";
$editCategory = null;


/* DELETE */

if (isset($_POST["delete_category"])) {

    $id = (int) $_POST["category_id"];

    $stmt = $pdo->prepare(
        "DELETE FROM categories
         WHERE id = ?"
    );

    $stmt->execute([
        $id
    ]);

    $message = "Category deleted.";
}


/* ADD */

if (isset($_POST["add_category"])) {

    $name =
        trim($_POST["category_name"] ?? "");


    if ($name === "") {

        $message =
            "Category name is required.";

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO categories
             (category_name)
             VALUES (?)"
        );

        $stmt->execute([
            $name
        ]);

        $message =
            "Category added.";
    }
}


/* UPDATE */

if (isset($_POST["update_category"])) {

    $id =
        (int) $_POST["category_id"];

    $name =
        trim($_POST["category_name"] ?? "");


    if ($name === "") {

        $message =
            "Category name is required.";

    } else {

        $stmt = $pdo->prepare(
            "UPDATE categories
             SET category_name = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $name,
            $id
        ]);

        $message =
            "Category updated.";
    }
}


/* EDIT */

if (isset($_GET["edit"])) {

    $id =
        (int) $_GET["edit"];


    $stmt = $pdo->prepare(
        "SELECT *
         FROM categories
         WHERE id = ?"
    );

    $stmt->execute([
        $id
    ]);

    $editCategory =
        $stmt->fetch();
}


/* FETCH */

$stmt = $pdo->query(
    "SELECT *
     FROM categories
     ORDER BY id DESC"
);

$categories =
    $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Categories - PetCare Hub</title>

<link
    rel="stylesheet"
    href="admin.css">

</head>


<body>


<?php require "admin_sidebar.php"; ?>


<div class="main">

    <h1 class="page-title">
        Categories
    </h1>


    <?php if ($message): ?>

        <div class="message">
            <?= clean($message) ?>
        </div>

    <?php endif; ?>


    <div class="box">

        <h2>
            <?= $editCategory
                ? "Edit Category"
                : "Add Category"
            ?>
        </h2>


        <form method="POST">


            <?php if ($editCategory): ?>

                <input
                    type="hidden"
                    name="category_id"
                    value="<?= (int)$editCategory["id"] ?>">

            <?php endif; ?>


            <div class="field">

                <label>
                    Category Name
                </label>

                <input
                    type="text"
                    name="category_name"
                    value="<?=
                        $editCategory
                        ? clean(
                            $editCategory["category_name"]
                        )
                        : ""
                    ?>"
                    placeholder="Food / Toys / Care"
                    required>

            </div>


            <div class="buttons">


                <?php if ($editCategory): ?>

                    <button
                        type="submit"
                        name="update_category">

                        Update Category

                    </button>


                    <a
                        class="cancel-btn"
                        href="categories.php">

                        Cancel

                    </a>

                <?php else: ?>

                    <button
                        type="submit"
                        name="add_category">

                        Add Category

                    </button>

                <?php endif; ?>


            </div>

        </form>

    </div>


    <div class="box">

        <h2>
            All Categories
        </h2>


        <?php if ($categories): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $categories
                    as $category
                ): ?>

                    <tr>

                        <td>
                            <?= (int)$category["id"] ?>
                        </td>

                        <td>
                            <?= clean(
                                $category["category_name"]
                            ) ?>
                        </td>

                        <td>

                            <a
                                class="edit-btn"
                                href="categories.php?edit=<?= (int)$category["id"] ?>">

                                Edit

                            </a>


                            <form
                                method="POST"
                                style="display:inline;">

                                <input
                                    type="hidden"
                                    name="category_id"
                                    value="<?= (int)$category["id"] ?>">


                                <button
                                    type="submit"
                                    name="delete_category"
                                    class="delete-btn"
                                    onclick="return confirm('Delete category?');">

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
                No categories found.
            </div>

        <?php endif; ?>

    </div>

</div>


</body>

</html>