<?php

function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function statusBadge($status) {
    if ($status == "Resolved") {
        $class = "status-resolved";
        $icon  = "bi-check-circle-fill";
    } elseif ($status == "In Progress") {
        $class = "status-progress";
        $icon  = "bi-arrow-repeat";
    } else {
        $class = "status-pending";
        $icon  = "bi-hourglass-split";
    }

    return '<span class="status-badge ' . $class . '"><i class="bi ' . $icon . '"></i> '
         . e($status) . '</span>';
}

function flashMessages() {
    return [
        "added"    => ["success", "bi-check-circle-fill",       "Concern added successfully."],
        "updated"  => ["success", "bi-check-circle-fill",       "Concern updated successfully."],
        "deleted"  => ["success", "bi-check-circle-fill",       "Concern deleted successfully."],
        "notfound" => ["danger",  "bi-exclamation-triangle-fill", "That concern could not be found."]
    ];
}
