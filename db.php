<?php

date_default_timezone_set("Asia/Manila");

$conn = mysqli_connect("localhost", "root", "", "barangay_concern");

if (!$conn) {
    die("Database Connection Error: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
