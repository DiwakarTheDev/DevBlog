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

// Get all tags
$tags = getAllTags($conn);

// Get post tags
$post_tags = getPostTags($conn, $post_id);
$selected_tag_ids = array_column($post_tags, 'id');

$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $selected_tags = $_POST['tags'] ?? [];
    
    // Validate input
    if (empty($title) || empty($content)) {
        $error = 'Please fill in all required fields';
    } else {
        // Handle image upload
        $image_name = $post['image']; // Keep existing image by default
        $thumbnail_name = $post['thumbnail']; // Keep existing thumbnail by default
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_result = uploadImage($_FILES['image'], '../uploads/');
            
            if ($upload_result['success']) {
                $image_name = $upload_result['filename'];
                
                // Create thumbnail
                $thumbnail_result = createThumbnail('../uploads/' . $image_name, '../uploads/thumbnails/', 400, 250);
                if ($thumbnail_result['success']) {
                    $thumbnail_name = $thumbnail_result['filename'];
                    
                    // Delete old image if exists
                    if (!empty($post['image']) && file_exists('../uploads/' . $post['image'])) {
                        unlink('../uploads/' . $post['image']);
                    }
                    
                    // Delete old thumbnail if exists
                    if (!empty($post['thumbnail']) && file_exists('../uploads/thumbnails/' . $post['thumbnail'])) {
                        unlink('../uploads/thumbnails/' . $post['thumbnail']);
                    }
                }
            } else {
                $error = $upload_result['error'];
            }
        }
        
        // Handle image deletion
        if (isset($_POST['delete_image']) && $_POST['delete_image'] === '1') {
            if (!empty($post['image']) && file_exists('../uploads/' . $post['image'])) {
                unlink('../uploads/' . $post['image']);
            }
            if (!empty($post['thumbnail']) && file_exists('../uploads/thumbnails/' . $post['thumbnail'])) {
                unlink('../uploads/thumbnails/' . $post['thumbnail']);
            }
            $image_name = '';
            $thumbnail_name = '';
        }
        
        if (empty($error)) {
            // Begin transaction
            $conn->begin_transaction();
            
            try {
                // Update post in database
                $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, image = ?, thumbnail = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $title, $content, $image_name, $thumbnail_name, $post_id);
                $stmt->execute();
                
                // Delete existing tag relationships
                $stmt = $conn->prepare("DELETE FROM post_tags WHERE post_id = ?");
                $stmt->bind_param("i", $post_id);
                $stmt->execute();
                
                // Add new tag relationships
                if (!empty($selected_tags)) {
                    foreach ($selected_tags as $tag_id) {
                        // Check if it's a new tag (string) or existing tag (integer)
                        if (!is_numeric($tag_id)) {
                            // Create new tag
                            $tag_name = trim($tag_id);
                            $tag_slug = createSlug($tag_name);
                            
                            $stmt = $conn->prepare("INSERT INTO tags (name, slug) VALUES (?, ?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
                            $stmt->bind_param("ss", $tag_name, $tag_slug);
                            $stmt->execute();
                            
                            $tag_id = $conn->insert_id;
                        }
                        
                        // Add tag to post
                        $stmt = $conn->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
                        $stmt->bind_param("ii", $post_id, $tag_id);
                        $stmt->execute();
                    }
                }
                
                // Commit transaction
                $conn->commit();
                
                $success = 'Post updated successfully';
                // Refresh post data
                $post = getPostById($conn, $post_id);
                $post_tags = getPostTags($conn, $post_id);
                $selected_tag_ids = array_column($post_tags, 'id');
            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollback();
                $error = 'Failed to update post: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - Dev Blog</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- Include QuillJS CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-content">
            <h1>Edit Post</h1>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data" class="post-form" id="post-form">
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="tags">Tags</label>
                    <select id="tags" name="tags[]" class="tag-select" multiple>
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?php echo $tag['id']; ?>" <?php echo in_array($tag['id'], $selected_tag_ids) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tag['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small>Select existing tags or type to create new ones</small>
                </div>
                
                <div class="form-group">
                    <label for="content">Content *</label>
                    <div id="editor-container" style="height: 400px;"></div>
                    <input type="hidden" name="content" id="content-input">
                </div>
                
                <div class="form-group">
                    <label for="image">Featured Image</label>
                    
                    <?php if (!empty($post['image'])): ?>
                        <div class="current-image">
                            <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Current featured image" style="max-width: 300px; max-height: 200px;">
                            <div class="image-actions">
                                <label>
                                    <input type="checkbox" name="delete_image" value="1"> Delete current image
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/gif">
                    <small>Max size: 2MB. Allowed formats: JPG, PNG, GIF</small>
                    <div id="image-preview" class="image-preview"></div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Post</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </main>
    </div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Include QuillJS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for tags
            $('.tag-select').select2({
                tags: true,
                tokenSeparators: [','],
                placeholder: 'Select or create tags',
                ajax: {
                    url: 'ajax/get-tags.php',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });
            
            // Initialize Quill editor
            var quill = new Quill('#editor-container', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'image', 'code-block'],
                        ['clean']
                    ]
                },
                placeholder: 'Write your content here...',
                theme: 'snow'
            });
            
            // Set initial content
            quill.root.innerHTML = <?php echo json_encode($post['content']); ?>;
            
            // Update hidden input with Quill content before form submission
            $('#post-form').on('submit', function() {
                $('#content-input').val(quill.root.innerHTML);
            });
            
            // Image preview
            $('#image').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image-preview').html('<img src="' + e.target.result + '" style="max-width: 300px; max-height: 200px;">');
                    }
                    reader.readAsDataURL(file);
                } else {
                    $('#image-preview').html('');
                }
            });
        });
    </script>
</body>
</html>

