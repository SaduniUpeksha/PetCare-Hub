<?php

require_once "../includes/db.php";
require_once "../includes/functions.php";

requireLogin();

$userId = $_SESSION["user_id"];
$message = "";

/* ADD PET */

if (isset($_POST["add_pet"])) {

    $petName = trim($_POST["pet_name"] ?? "");
    $type = trim($_POST["type"] ?? "");
    $breed = trim($_POST["breed"] ?? "");
    $age = $_POST["age"] !== "" ? (int) $_POST["age"] : null;
    $description = trim($_POST["description"] ?? "");

    if ($petName === "" || $type === "") {

        $message = "Pet name and type are required.";

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO pets
            (pet_name, type, breed, age, price, description, image, owner_id)
            VALUES (?, ?, ?, ?, 0, ?, '', ?)"
        );

        $stmt->execute([
            $petName,
            $type,
            $breed,
            $age,
            $description,
            $userId
        ]);

        $message = "Pet added successfully.";
    }
}

/* DELETE OWN PET */

if (isset($_POST["delete_pet"])) {

    $petId = (int) $_POST["pet_id"];

    $stmt = $pdo->prepare(
        "DELETE FROM pets
         WHERE id = ? AND owner_id = ?"
    );

    $stmt->execute([$petId, $userId]);

    $message = "Pet deleted successfully.";
}

/* FETCH MY PETS */

$stmt = $pdo->prepare(
    "SELECT * FROM pets
     WHERE owner_id = ?
     ORDER BY id DESC"
);

$stmt->execute([$userId]);

$pets = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Pets - PetCare Hub</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f3f6f0;
    margin: 0;
}

.container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 20px;
}

.box {
    background: white;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 25px;
}

h1, h2 {
    color: #1d4a3e;
}

input, textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 10px;
    margin-bottom: 12px;
}

button {
    background: #1d4a3e;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 7px;
}

.delete {
    background: #e1614a;
}

.message {
    background: #dff3df;
    padding: 12px;
    border-radius: 8px;
}

.pet {
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 12px;
    border-radius: 8px;
}

a {
    color: #1d4a3e;
}

</style>

</head>

<body>

<div class="container">

<h1>My Pets</h1>

<a href="dashboard.php">← Dashboard</a>

<br><br>

<?php if ($message): ?>

<div class="message">
<?= clean($message) ?>
</div>

<br>

<?php endif; ?>

<div class="box">

<h2>Add My Pet</h2>

<form method="POST">

<input type="text"
       name="pet_name"
       placeholder="Pet name"
       required>

<input type="text"
       name="type"
       placeholder="Dog / Cat / Rabbit"
       required>

<input type="text"
       name="breed"
       placeholder="Breed">

<input type="number"
       name="age"
       min="0"
       placeholder="Age">

<textarea name="description"
          placeholder="Description"></textarea>

<button type="submit" name="add_pet">
Add Pet
</button>

</form>

</div>

<div class="box">

<h2>My Pets</h2>

<?php if (!$pets): ?>

<p>No pets added yet.</p>

<?php else: ?>

<?php foreach ($pets as $pet): ?>

<div class="pet">

<strong><?= clean($pet["pet_name"]) ?></strong>

<p>
Type: <?= clean($pet["type"]) ?>
</p>

<p>
Breed: <?= clean($pet["breed"]) ?>
</p>

<p>
Age: <?= clean($pet["age"]) ?>
</p>

<form method="POST"
      onsubmit="return confirm('Delete this pet?');">

<input type="hidden"
       name="pet_id"
       value="<?= $pet["id"] ?>">

<button class="delete"
        type="submit"
        name="delete_pet">
Delete
</button>

</form>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>

</div>

</body>
</html>