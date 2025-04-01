<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get post details
$post = getPostById($conn, $post_id);

// If post doesn't exist, redirect to homepage
if (!$post) {
    header('Location: index.php');
    exit;
}

// Get post tags
$post_tags = getPostTags($conn, $post_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Dev Blog</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <article class="single-post">
            <header class="post-header">
                <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                
                <?php if (count($post_tags) > 0): ?>
                <div class="post-tags">
                    <?php foreach ($post_tags as $tag): ?>
                        <a href="tag.php?slug=<?php echo $tag['slug']; ?>" class="tag"><?php echo htmlspecialchars($tag['name']); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="post-meta">
                    <span class="date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                </div>
            </header>

            <?php if (!empty($post['image'])): ?>
                <div class="post-image">
                    <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                </div>
            <?php endif; ?>

            <div class="post-content">
                <?php echo $post['content']; ?>
            </div>
        </article>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>

