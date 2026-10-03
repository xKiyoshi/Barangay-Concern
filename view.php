<?php
include "db.php";
include "functions.php";

$currentPage = "";

// GET CONCERN ID
if (!isset($_GET['id'])) {
    header("Location: index.php?msg=notfound");
    exit();
}

$id = (int) $_GET['id'];

// GET CONCERN DATA
$stmt = mysqli_prepare($conn, "SELECT * FROM concerns WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php?msg=notfound");
    exit();
}

$concern = mysqli_fetch_assoc($result);
$time = strtotime($concern['date_created']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Concern - Barangay Concern Reporting System</title>
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
                <h1 class="h3 page-title mt-2">Concern Details</h1>
                <p class="page-subtitle">Concern #<?php echo (int) $concern['id']; ?></p>
            </div>

            <div class="card page-card">
                <div class="card-body p-4">

                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
                        <h2 class="h4 mb-0"><?php echo e($concern['title']); ?></h2>
                        <?php echo statusBadge($concern['status']); ?>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="detail-label">Reported by</div>
                            <div class="detail-value"><?php echo e($concern['name']); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Category</div>
                            <div class="detail-value"><?php echo e($concern['category']); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Location</div>
                            <div class="detail-value"><?php echo e($concern['location']); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Date submitted</div>
                            <div class="detail-value">
                                <?php echo date("F d, Y", $time) . " at " . date("g:i A", $time); ?>
                            </div>
                        </div>
                    </div>

                    <div class="detail-label">Description</div>
                    <div class="description-box"><?php echo nl2br(e($concern['description'])); ?></div>

                    <hr class="my-4">

                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <a href="edit.php?id=<?php echo (int) $concern['id']; ?>" class="btn btn-primary px-4">
                            <i class="bi bi-pencil-square me-1"></i>Edit
                        </a>
                        <form action="delete.php" method="POST" class="d-grid"
                              onsubmit="return confirm('Are you sure you want to delete this concern?')">
                            <input type="hidden" name="id" value="<?php echo (int) $concern['id']; ?>">
                            <button type="submit" class="btn btn-outline-danger px-4">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </form>
                        <a href="index.php" class="btn btn-outline-secondary px-4">Back</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
