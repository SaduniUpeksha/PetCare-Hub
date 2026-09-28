<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireAdmin();

$message = "";
$editProduct = null;


/* DELETE */

if (isset($_POST["delete_product"])) {

    $productId =
        (int) $_POST["product_id"];


    $stmt = $pdo->prepare(
        "DELETE FROM products
         WHERE id = ?"
    );

    $stmt->execute([
        $productId
    ]);

    $message =
        "Product deleted successfully.";
}


/* ADD */

if (isset($_POST["add_product"])) {

    $productName =
        trim($_POST["product_name"] ?? "");

    $category =
        trim($_POST["category"] ?? "");

    $price =
        $_POST["price"] ?? "";

    $quantity =
        $_POST["quantity"] ?? 0;

    $image =
        trim($_POST["image"] ?? "");


    if (
        $productName === "" ||
        $price === ""
    ) {

        $message =
            "Product name and price are required.";

    } elseif (
        !is_numeric($price) ||
        $price < 0
    ) {

        $message =
            "Please enter a valid price.";

    } elseif (
        !is_numeric($quantity) ||
        $quantity < 0
    ) {

        $message =
            "Please enter a valid quantity.";

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO products
            (
                product_name,
                category,
                price,
                quantity,
                image
            )
            VALUES (?, ?, ?, ?, ?)"
        );


        $stmt->execute([
            $productName,
            $category,
            $price,
            (int)$quantity,
            $image
        ]);


        $message =
            "Product added successfully.";
    }
}


/* UPDATE */

if (isset($_POST["update_product"])) {

    $productId =
        (int) $_POST["product_id"];

    $productName =
        trim($_POST["product_name"] ?? "");

    $category =
        trim($_POST["category"] ?? "");

    $price =
        $_POST["price"] ?? "";

    $quantity =
        $_POST["quantity"] ?? 0;

    $image =
        trim($_POST["image"] ?? "");


    if (
        $productName === "" ||
        $price === ""
    ) {

        $message =
            "Product name and price are required.";

    } elseif (
        !is_numeric($price) ||
        $price < 0
    ) {

        $message =
            "Please enter a valid price.";

    } elseif (
        !is_numeric($quantity) ||
        $quantity < 0
    ) {

        $message =
            "Please enter a valid quantity.";

    } else {

        $stmt = $pdo->prepare(
            "UPDATE products
             SET
                product_name = ?,
                category = ?,
                price = ?,
                quantity = ?,
                image = ?
             WHERE id = ?"
        );


        $stmt->execute([
            $productName,
            $category,
            $price,
            (int)$quantity,
            $image,
            $productId
        ]);


        $message =
            "Product updated successfully.";
    }
}


/* LOAD EDIT */

if (isset($_GET["edit"])) {

    $productId =
        (int) $_GET["edit"];


    $stmt = $pdo->prepare(
        "SELECT *
         FROM products
         WHERE id = ?"
    );

    $stmt->execute([
        $productId
    ]);


    $editProduct =
        $stmt->fetch();
}


/* FETCH */

$stmt = $pdo->query(
    "SELECT *
     FROM products
     ORDER BY id DESC"
);

$products =
    $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Manage Products - PetCare Hub</title>

<link
    rel="stylesheet"
    href="admin.css">

</head>


<body>


<?php require "admin_sidebar.php"; ?>


<div class="main">

    <h1 class="page-title">
        Product Management
    </h1>


    <?php if ($message): ?>

        <div class="message">
            <?= clean($message) ?>
        </div>

    <?php endif; ?>


    <div class="box">


        <?php if ($editProduct): ?>


            <h2>
                Edit Product
            </h2>


            <form method="POST">


                <input
                    type="hidden"
                    name="product_id"
                    value="<?= (int)$editProduct["id"] ?>">


                <div class="form-grid">


                    <div class="field">

                        <label>
                            Product Name
                        </label>

                        <input
                            type="text"
                            name="product_name"
                            value="<?= clean($editProduct["product_name"]) ?>"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Category
                        </label>

                        <input
                            type="text"
                            name="category"
                            value="<?= clean($editProduct["category"]) ?>"
                            placeholder="Food, Toys, Medicine...">

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
                            value="<?= clean($editProduct["price"]) ?>"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Quantity
                        </label>

                        <input
                            type="number"
                            name="quantity"
                            min="0"
                            value="<?= clean($editProduct["quantity"]) ?>"
                            required>

                    </div>


                    <div class="field full">

                        <label>
                            Image Filename
                        </label>

                        <input
                            type="text"
                            name="image"
                            value="<?= clean($editProduct["image"]) ?>"
                            placeholder="example.jpg">

                    </div>


                </div>


                <div class="buttons">


                    <button
                        type="submit"
                        name="update_product">

                        Update Product

                    </button>


                    <a
                        class="cancel-btn"
                        href="products.php">

                        Cancel

                    </a>


                </div>


            </form>


        <?php else: ?>


            <h2>
                Add New Product
            </h2>


            <form method="POST">


                <div class="form-grid">


                    <div class="field">

                        <label>
                            Product Name
                        </label>

                        <input
                            type="text"
                            name="product_name"
                            placeholder="e.g. Premium Dog Food"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Category
                        </label>

                        <input
                            type="text"
                            name="category"
                            placeholder="Food / Toys / Care">

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
                            placeholder="e.g. 2500"
                            required>

                    </div>


                    <div class="field">

                        <label>
                            Quantity
                        </label>

                        <input
                            type="number"
                            name="quantity"
                            min="0"
                            value="0"
                            required>

                    </div>


                    <div class="field full">

                        <label>
                            Image Filename
                        </label>

                        <input
                            type="text"
                            name="image"
                            placeholder="example.jpg">

                    </div>


                </div>


                <div class="buttons">

                    <button
                        type="submit"
                        name="add_product">

                        Add Product

                    </button>

                </div>


            </form>

        <?php endif; ?>


    </div>


    <div class="box">

        <h2>
            All Products
        </h2>


        <?php if ($products): ?>


        <div class="table-wrap">


            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Product</th>

                        <th>Category</th>

                        <th>Price</th>

                        <th>Quantity</th>

                        <th>Image</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>


                <?php foreach (
                    $products
                    as $product
                ): ?>


                    <tr>

                        <td>
                            <?= (int)$product["id"] ?>
                        </td>

                        <td>
                            <?= clean(
                                $product["product_name"]
                            ) ?>
                        </td>

                        <td>
                            <?= clean(
                                $product["category"]
                            ) ?>
                        </td>

                        <td>
                            Rs.
                            <?= number_format(
                                $product["price"],
                                2
                            ) ?>
                        </td>

                        <td>
                            <?= (int)$product["quantity"] ?>
                        </td>

                        <td>
                            <?= clean(
                                $product["image"] ?? "-"
                            ) ?>
                        </td>


                        <td>


                            <a
                                class="edit-btn"
                                href="products.php?edit=<?= (int)$product["id"] ?>">

                                Edit

                            </a>


                            <form
                                method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Delete this product?');">


                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?= (int)$product["id"] ?>">


                                <button
                                    type="submit"
                                    name="delete_product"
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
                No products found.
            </div>

        <?php endif; ?>


    </div>

</div>


</body>

</html>