<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] != "POST" || !isset($_POST['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int) $_POST['id'];

// DELETE CONCERN
$stmt = mysqli_prepare($conn, "DELETE FROM concerns WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: index.php?msg=deleted");
    exit();
} else {
    die("Error deleting concern: " . mysqli_error($conn));
}
