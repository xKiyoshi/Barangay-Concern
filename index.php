<?php
include "db.php";
include "functions.php";

$currentPage = "home";

$categories = ["Garbage", "Drainage", "Road Damage", "Streetlight", "Noise Complaint", "Others"];
$statuses   = ["Pending", "In Progress", "Resolved"];

// ---------- SUCCESS / ERROR MESSAGE ----------
$flash = flashMessages();
$msgKey = isset($_GET['msg']) ? $_GET['msg'] : "";
$alert  = isset($flash[$msgKey]) ? $flash[$msgKey] : null;

// ---------- SEARCH AND FILTERS ----------
$search   = isset($_GET['search'])   ? trim($_GET['search']) : "";
$status   = isset($_GET['status'])   ? $_GET['status']      : "";
$category = isset($_GET['category']) ? $_GET['category']    : "";

// Ignore values that are not in our lists
if (!in_array($status, $statuses))     { $status = ""; }
if (!in_array($category, $categories)) { $category = ""; }

$hasFilters = ($search != "" || $status != "" || $category != "");

// ---------- SUMMARY COUNTS ----------
$countResult = mysqli_query($conn,
    "SELECT COUNT(*) AS total,
            COALESCE(SUM(status = 'Pending'), 0)     AS pending,
            COALESCE(SUM(status = 'In Progress'), 0) AS in_progress,
            COALESCE(SUM(status = 'Resolved'), 0)    AS resolved
     FROM concerns");
$counts = mysqli_fetch_assoc($countResult);

// ---------- BUILD THE LIST QUERY ----------
$sql    = "SELECT * FROM concerns";
$where  = [];
$types  = "";
$params = [];

if ($search != "") {
    $where[] = "(name LIKE ? OR title LIKE ? OR location LIKE ?)";
    $like = "%" . $search . "%";
    $types .= "sss";
    array_push($params, $like, $like, $like);
}

if ($status != "") {
    $where[] = "status = ?";
    $types .= "s";
    $params[] = $status;
}

if ($category != "") {
    $where[] = "category = ?";
    $types .= "s";
    $params[] = $category;
}

if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($types != "") {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rowCount = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barangay Concern Reporting System</title>
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

    <!-- Success / error alert -->
    <?php if ($alert) { ?>
        <div class="alert alert-<?php echo $alert[0]; ?> alert-dismissible fade show alert-auto-dismiss" role="alert">
            <i class="bi <?php echo $alert[1]; ?> me-2"></i><?php echo e($alert[2]); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <!-- Page heading -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 page-title">Submitted Concerns</h1>
            <p class="page-subtitle">Review, search, and update concerns reported by residents.</p>
        </div>
        <a href="add.php" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Add Concern
        </a>
    </div>

    <!-- Summary cards -->
    <div class="row row-cols-2 row-cols-lg-4 g-3 mb-4">
        <div class="col">
            <a href="index.php" class="stat-card <?php if ($status == "") echo "active"; ?>">
                <div class="stat-icon icon-total"><i class="bi bi-clipboard-data"></i></div>
                <div>
                    <div class="stat-number"><?php echo (int) $counts['total']; ?></div>
                    <div class="stat-label">Total Concerns</div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?status=Pending" class="stat-card <?php if ($status == "Pending") echo "active"; ?>">
                <div class="stat-icon icon-pending"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="stat-number"><?php echo (int) $counts['pending']; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?status=In+Progress" class="stat-card <?php if ($status == "In Progress") echo "active"; ?>">
                <div class="stat-icon icon-progress"><i class="bi bi-arrow-repeat"></i></div>
                <div>
                    <div class="stat-number"><?php echo (int) $counts['in_progress']; ?></div>
                    <div class="stat-label">In Progress</div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?status=Resolved" class="stat-card <?php if ($status == "Resolved") echo "active"; ?>">
                <div class="stat-icon icon-resolved"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="stat-number"><?php echo (int) $counts['resolved']; ?></div>
                    <div class="stat-label">Resolved</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Search and filters -->
    <div class="card page-card mb-4">
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-2">

                <div class="col-12 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Search name, concern, or location"
                               value="<?php echo e($search); ?>">
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <select name="status" class="form-select" aria-label="Filter by status">
                        <option value="">All Statuses</option>
                        <?php foreach ($statuses as $s) { ?>
                            <option value="<?php echo e($s); ?>" <?php if ($status == $s) echo "selected"; ?>>
                                <?php echo e($s); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-6 col-lg-3">
                    <select name="category" class="form-select" aria-label="Filter by category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $c) { ?>
                            <option value="<?php echo e($c); ?>" <?php if ($category == $c) echo "selected"; ?>>
                                <?php echo e($c); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-12 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Search</button>
                    <?php if ($hasFilters) { ?>
                        <a href="index.php" class="btn btn-outline-secondary" title="Clear search and filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    <?php } ?>
                </div>

            </form>
        </div>
    </div>

    <!-- Concerns list -->
    <div class="card page-card">

        <?php if ($rowCount == 0) { ?>

            <!-- Empty state -->
            <div class="card-body">
                <div class="empty-state">
                    <?php if ($hasFilters) { ?>
                        <div class="empty-icon"><i class="bi bi-search"></i></div>
                        <h2 class="h5">No matching concerns</h2>
                        <p class="text-muted mb-3">Try a different keyword or clear the filters.</p>
                        <a href="index.php" class="btn btn-outline-primary">Clear search and filters</a>
                    <?php } else { ?>
                        <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                        <h2 class="h5">No concerns yet</h2>
                        <p class="text-muted mb-3">When a resident reports a concern, it will show up here.</p>
                        <a href="add.php" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Add the first concern
                        </a>
                    <?php } ?>
                </div>
            </div>

        <?php } else { ?>

            <div class="card-header">
                Showing <?php echo $rowCount; ?> concern<?php echo $rowCount == 1 ? "" : "s"; ?>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell ps-4">ID</th>
                            <th class="d-none d-md-table-cell">Name</th>
                            <th>Concern</th>
                            <th class="d-none d-md-table-cell">Category</th>
                            <th class="d-none d-lg-table-cell">Location</th>
                            <th>Status</th>
                            <th class="d-none d-md-table-cell">Date</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td class="d-none d-md-table-cell ps-4"><?php echo (int) $row['id']; ?></td>
                            <td class="d-none d-md-table-cell"><?php echo e($row['name']); ?></td>
                            <td>
                                <a href="view.php?id=<?php echo (int) $row['id']; ?>"
                                   class="concern-title text-decoration-none text-reset">
                                    <?php echo e($row['title']); ?>
                                </a>
                                <!-- Extra details shown only on small screens -->
                                <div class="concern-meta d-md-none">
                                    <?php echo e($row['name']); ?> &bull; <?php echo e($row['category']); ?>
                                </div>
                                <div class="concern-meta d-lg-none">
                                    <i class="bi bi-geo-alt"></i> <?php echo e($row['location']); ?>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell"><?php echo e($row['category']); ?></td>
                            <td class="d-none d-lg-table-cell"><?php echo e($row['location']); ?></td>
                            <td><?php echo statusBadge($row['status']); ?></td>
                            <td class="d-none d-md-table-cell text-nowrap">
                                <?php echo date("M d, Y", strtotime($row['date_created'])); ?>
                            </td>
                            <td class="text-end text-nowrap pe-3">
                                <a href="view.php?id=<?php echo (int) $row['id']; ?>"
                                   class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo (int) $row['id']; ?>"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="delete.php" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this concern?')">
                                    <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
            </div>

        <?php } ?>

    </div>

    <footer class="text-center py-4">Barangay Concern Reporting System</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
