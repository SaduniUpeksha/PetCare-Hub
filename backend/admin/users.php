<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireAdmin();

$stmt = $pdo->query(
    "SELECT
        id,
        username,
        email,
        created_at
     FROM users
     WHERE role = 'customer'
     ORDER BY id DESC"
);

$users = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Manage Users - PetCare Hub</title>

<link
    rel="stylesheet"
    href="admin.css">

</head>


<body>


<?php require "admin_sidebar.php"; ?>


<div class="main">

    <h1 class="page-title">
        Registered Users
    </h1>


    <div class="box">

        <?php if ($users): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Username</th>

                        <th>Email</th>

                        <th>Created</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($users as $user): ?>

                    <tr>

                        <td>
                            <?= (int)$user["id"] ?>
                        </td>

                        <td>
                            <?= clean($user["username"]) ?>
                        </td>

                        <td>
                            <?= clean($user["email"]) ?>
                        </td>

                        <td>
                            <?= clean($user["created_at"]) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <?php else: ?>

            <div class="empty">
                No registered customers found.
            </div>

        <?php endif; ?>

    </div>

</div>


</body>

</html>