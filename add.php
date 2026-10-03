<?php
include "db.php";
include "functions.php";

$currentPage = "add";
$message = "";
$categories = ["Garbage", "Drainage", "Road Damage", "Streetlight", "Noise Complaint", "Others"];

$name = $title = $category = $description = $location = "";

if (isset($_POST['add'])) {

    $name        = trim($_POST['name']);
    $title       = trim($_POST['title']);
    $category    = $_POST['category'];
    $description = str_replace("\r\n", "\n", trim($_POST['description']));
    $location    = trim($_POST['location']);
    $status      = "Pending"; // Default status

    if ($name == "" || $title == "" || $description == "" || $location == "") {
        $message = "Please fill in all required fields.";
    } elseif (!in_array($category, $categories)) {
        $message = "Please choose a valid category.";
    } elseif (mb_strlen($description) > 500) {
        $message = "Description must be 500 characters or less.";
    } else {

        $stmt = mysqli_prepare($conn,
            "INSERT INTO concerns (name, title, category, description, location, status)
             VALUES (?, ?, ?, ?, ?, ?)");

        mysqli_stmt_bind_param($stmt, "ssssss",
            $name, $title, $category, $description, $location, $status);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?msg=added");
            exit();
        } else {
            $message = "Error adding concern: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Concern - Barangay Concern Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-sm navbar-dark app-navbar sticky-top mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-megaphone-fill me-2"></i>Barangay Concerns
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav" aria-controls="mainNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php if ($currentPage == "home") echo "active"; ?>" href="index.php">
                        <i class="bi bi-list-ul me-1"></i>All Concerns
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <div class="mb-4">
                <a href="index.php" class="text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i>Back to all concerns
                </a>
                <h1 class="h3 page-title mt-2">Add Concern</h1>
                <p class="page-subtitle">Fill in the details below. New concerns start as Pending.</p>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">

                    <?php if ($message != "") { ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e($message); ?>
                        </div>
                    <?php } ?>

                    <form method="POST">
                        <div class="row g-3">

                            <div class="col-12">
                                <label for="name" class="form-label required">Name</label>
                                <input type="text" id="name" name="name" class="form-control" maxlength="100"
                                       placeholder="Full name of the resident"
                                       value="<?php echo e($name); ?>" required>
                            </div>

                            <div class="col-12">
                                <label for="title" class="form-label required">Concern Title</label>
                                <input type="text" id="title" name="title" class="form-control" maxlength="150"
                                       placeholder="Short summary, e.g. Broken streetlight"
                                       value="<?php echo e($title); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="category" class="form-label required">Category</label>
                                <select id="category" name="category" class="form-select" required>
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $c) { ?>
                                        <option value="<?php echo e($c); ?>"
                                            <?php if ($category == $c) echo "selected"; ?>>
                                            <?php echo e($c); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" id="status" class="form-control" value="Pending" disabled>
                            </div>

                            <div class="col-12">
                                <label for="location" class="form-label required">Location</label>
                                <input type="text" id="location" name="location" class="form-control" maxlength="150"
                                       placeholder="Purok, street, or landmark"
                                       value="<?php echo e($location); ?>" required>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label required">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="5"
                                          maxlength="500" placeholder="Describe the concern in detail"
                                          required><?php echo e($description); ?></textarea>
                                <div class="form-text text-end"><span id="descCount">0</span>/500 characters</div>
                            </div>

                        </div>

                        <hr class="my-4">

                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" name="add" class="btn btn-primary px-4">
                                <i class="bi bi-send me-1"></i>Submit Concern
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
