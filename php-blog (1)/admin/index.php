<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get posts for admin dashboard
$posts = getAllPosts($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dev Blog</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-content">
            <h1>Dashboard</h1>
            
            <div class="admin-actions">
                <a href="create-post.php" class="btn btn-primary">Create New Post</a>
            </div>
            
            <div class="posts-table">
                <h2>Manage Posts</h2>
                
                <?php if (count($posts) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><?php echo $post['id']; ?></td>
                                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                                    <td class="actions">
                                        <a href="../post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-view" target="_blank">View</a>
                                        <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                        <a href="delete-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No posts found. <a href="create-post.php">Create your first post</a>.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>

