<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get latest posts for homepage
$posts = getLatestPosts($conn, 6);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Blog</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <section class="hero">
            <h1>Welcome to Dev Blog</h1>
            <p>A place for developers to share knowledge and experiences</p>
        </section>

        <section class="posts">
            <h2>Latest Posts</h2>
            
            <?php if (count($posts) > 0): ?>
                <div class="post-grid">
                    <?php foreach ($posts as $post): ?>
                        <article class="post-card">
                            <div class="post-image">
                                <?php if (!empty($post['thumbnail'])): ?>
                                    <img src="uploads/thumbnails/<?php echo htmlspecialchars($post['thumbnail']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                <?php elseif (!empty($post['image'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                <?php else: ?>
                                    <img src="assets/images/placeholder.jpg" alt="Placeholder">
                                <?php endif; ?>
                            </div>
                            <div class="post-content">
                                <h3><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                                
                                <?php 
                                // Get post tags
                                $post_tags = getPostTags($conn, $post['id']); 
                                if (count($post_tags) > 0): 
                                ?>
                                <div class="post-tags">
                                    <?php foreach ($post_tags as $tag): ?>
                                        <a href="tag.php?slug=<?php echo $tag['slug']; ?>" class="tag"><?php echo htmlspecialchars($tag['name']); ?></a>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="post-meta">
                                    <span class="date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                                </div>
                                <p><?php echo substr(strip_tags($post['content']), 0, 150); ?>...</p>
                                <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">Read More</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No posts found.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>

