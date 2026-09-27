<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireLogin();

$userId = $_SESSION["user_id"];
$username = $_SESSION["username"];

$message = "";


/* =========================================
   UPDATE QUANTITY
========================================= */

if (isset($_POST["update_cart"])) {

    $productId =
        (int) $_POST["product_id"];

    $quantity =
        (int) $_POST["quantity"];


    if ($quantity < 1) {
        $quantity = 1;
    }


    $stmt = $pdo->prepare(
        "SELECT quantity
         FROM products
         WHERE id = ?"
    );

    $stmt->execute([
        $productId
    ]);


    $product =
        $stmt->fetch();


    if ($product) {

        if (
            $quantity >
            $product["quantity"]
        ) {

            $quantity =
                $product["quantity"];
        }


        $stmt = $pdo->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE user_id = ?
             AND product_id = ?"
        );


        $stmt->execute([

            $quantity,

            $userId,

            $productId

        ]);
    }
}


/* =========================================
   REMOVE
========================================= */

if (isset($_POST["remove_item"])) {

    $productId =
        (int) $_POST["product_id"];


    $stmt = $pdo->prepare(
        "DELETE FROM cart_items
         WHERE user_id = ?
         AND product_id = ?"
    );


    $stmt->execute([

        $userId,

        $productId

    ]);


    $message =
        "Item removed.";
}


/* =========================================
   CLEAR
========================================= */

if (isset($_POST["clear_cart"])) {

    $stmt = $pdo->prepare(
        "DELETE FROM cart_items
         WHERE user_id = ?"
    );


    $stmt->execute([
        $userId
    ]);


    $message =
        "Cart cleared.";
}


/* =========================================
   FETCH CART
========================================= */

$stmt = $pdo->prepare(
    "SELECT
        c.product_id,
        c.quantity,
        p.product_name,
        p.price,
        p.image,
        p.quantity AS stock,
        (c.quantity * p.price) AS subtotal

     FROM cart_items c

     JOIN products p
       ON c.product_id = p.id

     WHERE c.user_id = ?

     ORDER BY c.id DESC"
);


$stmt->execute([
    $userId
]);


$items =
    $stmt->fetchAll();


/* =========================================
   TOTAL
========================================= */

$total = 0;
$cartCount = 0;


foreach ($items as $item) {

    $total +=
        (float) $item["subtotal"];

    $cartCount +=
        (int) $item["quantity"];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>My Cart - PetCare Hub</title>


<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">


<link
    rel="stylesheet"
    href="../../frontend/css/common.css">

</head>


<body>


<!-- SAME NAVBAR -->

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


<ul
    class="navbar-nav ms-auto align-items-center">


<li class="nav-item">

<a
    class="nav-link"
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
    class="nav-link active"
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


<!-- CART CONTENT -->

<div class="container py-5">


<h1 class="fw-bold mb-4">
    My Cart 🛒
</h1>


<?php if ($message): ?>

<div class="alert alert-success">

    <?= clean($message) ?>

</div>

<?php endif; ?>


<?php if (!$items): ?>


<div class="alert alert-info">

    Your cart is empty.

    <br><br>

    <a
        href="../../frontend/products.html"
        class="btn btn-primary">

        Browse Products

    </a>

</div>


<?php else: ?>


<div class="card shadow-sm">


<div class="card-body">


<?php foreach ($items as $item): ?>


<div
    class="row align-items-center border-bottom py-3">


<div class="col-md-4">

<strong>

    <?= clean(
        $item["product_name"]
    ) ?>

</strong>

</div>


<div class="col-md-2">

    Rs.
    <?= number_format(
        $item["price"],
        2
    ) ?>

</div>


<div class="col-md-3">


<form
    method="POST"
    class="d-flex gap-2">


<input
    type="hidden"
    name="product_id"
    value="<?= (int)$item["product_id"] ?>">


<input
    type="number"
    name="quantity"
    value="<?= (int)$item["quantity"] ?>"
    min="1"
    max="<?= (int)$item["stock"] ?>"
    class="form-control"
    style="width:90px;">


<button
    type="submit"
    name="update_cart"
    class="btn btn-primary btn-sm">

    Update

</button>


</form>

</div>


<div class="col-md-2">

<strong>

    Rs.
    <?= number_format(
        $item["subtotal"],
        2
    ) ?>

</strong>

</div>


<div class="col-md-1">


<form method="POST">


<input
    type="hidden"
    name="product_id"
    value="<?= (int)$item["product_id"] ?>">


<button
    type="submit"
    name="remove_item"
    class="btn btn-danger btn-sm">

    ×

</button>


</form>

</div>


</div>


<?php endforeach; ?>


<div class="text-end mt-4">


<h3>

    Total:
    Rs.
    <?= number_format(
        $total,
        2
    ) ?>

</h3>


<p class="text-muted">

    Payment will be completed at the
    PetCare Hub store.

</p>


<form method="POST">

<button
    type="submit"
    name="clear_cart"
    class="btn btn-outline-danger">

    Clear Cart

</button>

</form>


</div>


</div>

</div>


<?php endif; ?>


</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>