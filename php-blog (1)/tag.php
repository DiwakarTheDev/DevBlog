<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get tag slug from URL
$tag_slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Get tag details
$tag = null;
$posts = [];

if (!empty($tag_slug)) {
    $stmt = $conn->prepare("SELECT * FROM tags WHERE slug = ?");
    $stmt->bind_param("s", $tag_slug);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $tag = $result->fetch_assoc();
        
        // Get posts with this tag
        $stmt = $conn->prepare("
            SELECT p.* FROM posts p
            JOIN post_tags pt ON p.id = pt.post_id
            WHERE pt.tag_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->bind_param("i", $tag['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
        }
    }
}

// If tag doesn't exist, redirect to homepage
if (!$tag) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts tagged with "<?php echo htmlspecialchars($tag['name']); ?>" - Dev Blog</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <section class="tag-header">
            <h1>Posts tagged with "<?php echo htmlspecialchars($tag['name']); ?>"</h1>
        </section>

        <section class="posts">
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
                <p>No posts found with this tag.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>

