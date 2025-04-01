<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Process tag deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $tag_id = (int)$_GET['delete'];
    
    // Check if tag exists
    $stmt = $conn->prepare("SELECT * FROM tags WHERE id = ?");
    $stmt->bind_param("i", $tag_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        // Delete tag
        $stmt = $conn->prepare("DELETE FROM tags WHERE id = ?");
        $stmt->bind_param("i", $tag_id);
        
        if ($stmt->execute()) {
            $success = 'Tag deleted successfully';
        } else {
            $error = 'Failed to delete tag';
        }
    } else {
        $error = 'Tag not found';
    }
}

// Process tag creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $tag_name = trim($_POST['name'] ?? '');
    
    if (empty($tag_name)) {
        $error = 'Tag name cannot be empty';
    } else {
        $tag_slug = createSlug($tag_name);
        
        // Check if tag already exists
        $stmt = $conn->prepare("SELECT * FROM tags WHERE name = ? OR slug = ?");
        $stmt->bind_param("ss", $tag_name, $tag_slug);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $error = 'Tag already exists';
        } else {
            // Create tag
            $stmt = $conn->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
            $stmt->bind_param("ss", $tag_name, $tag_slug);
            
            if ($stmt->execute()) {
                $success = 'Tag created successfully';
            } else {
                $error = 'Failed to create tag';
            }
        }
    }
}

// Get all tags
$tags = getAllTags($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tags - Dev Blog</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-content">
            <h1>Manage Tags</h1>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="admin-card">
                <h2>Create New Tag</h2>
                <form method="POST" action="" class="inline-form">
                    <input type="hidden" name="action" value="create">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Tag name" required>
                        <button type="submit" class="btn btn-primary">Create Tag</button>
                    </div>
                </form>
            </div>
            
            <div class="admin-card">
                <h2>All Tags</h2>
                
                <?php if (count($tags) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tags as $tag): ?>
                                <tr>
                                    <td><?php echo $tag['id']; ?></td>
                                    <td><?php echo htmlspecialchars($tag['name']); ?></td>
                                    <td><?php echo htmlspecialchars($tag['slug']); ?></td>
                                    <td>
                                        <a href="../tag.php?slug=<?php echo $tag['slug']; ?>" class="btn btn-sm btn-view" target="_blank">View</a>
                                        <a href="manage-tags.php?delete=<?php echo $tag['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this tag?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No tags found.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>

