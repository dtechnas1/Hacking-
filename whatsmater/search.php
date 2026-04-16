<?php
/**
 * WhatsMater - AJAX Search Endpoint
 * Returns JSON array of matching users.
 */
require_once __DIR__ . '/config/app.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode([]);
    exit;
}

$query = sanitize($_GET['q'] ?? '');

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$searchParam = "%$query%";
$stmt = $conn->prepare("SELECT id, username, full_name, profile_pic FROM users WHERE status = 'active' AND (full_name LIKE ? OR username LIKE ?) ORDER BY full_name ASC LIMIT 10");
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $row['profile_pic'] = getProfilePic($row['profile_pic']);
    $users[] = $row;
}

echo json_encode($users);
