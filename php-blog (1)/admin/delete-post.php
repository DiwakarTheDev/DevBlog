<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get post details
$post = getPostById($conn, $post_id);

// If post doesn't exist, redirect to admin dashboard
if (!$post) {
    header('Location: index.php');
    exit;
}

// Delete post image if exists
if (!empty($post['image']) && file_exists('../uploads/' . $post['image'])) {
    unlink('../uploads/' . $post['image']);
}

// Delete post from database
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();

// Redirect to admin dashboard with success message
$_SESSION['message'] = 'Post deleted successfully';
header('Location: index.php');
exit;

