<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get search term
$search = isset($_GET['q']) ? $_GET['q'] : '';

// Get matching tags
$tags = [];

if (!empty($search)) {
    $search = '%' . $search . '%';
    $stmt = $conn->prepare("SELECT id, name FROM tags WHERE name LIKE ? ORDER BY name ASC LIMIT 10");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $tags[] = [
            'id' => $row['id'],
            'text' => $row['name']
        ];
    }
} else {
    // Get all tags if no search term
    $stmt = $conn->prepare("SELECT id, name FROM tags ORDER BY name ASC LIMIT 20");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $tags[] = [
            'id' => $row['id'],
            'text' => $row['name']
        ];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($tags);

