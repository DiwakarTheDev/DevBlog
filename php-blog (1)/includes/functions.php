<?php
/**
 * Check if user is logged in
 * 
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get all posts from database
 * 
 * @param mysqli $conn Database connection
 * @return array
 */
function getAllPosts($conn) {
    $posts = [];
    
    $query = "SELECT * FROM posts ORDER BY created_at DESC";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    
    return $posts;
}

/**
 * Get latest posts from database
 * 
 * @param mysqli $conn Database connection
 * @param int $limit Number of posts to retrieve
 * @return array
 */
function getLatestPosts($conn, $limit = 5) {
    $posts = [];
    
    $query = "SELECT * FROM posts ORDER BY created_at DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    
    return $posts;
}

/**
 * Get post by ID
 * 
 * @param mysqli $conn Database connection
 * @param int $id Post ID
 * @return array|null
 */
function getPostById($conn, $id) {
    $query = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Get all tags
 * 
 * @param mysqli $conn Database connection
 * @return array
 */
function getAllTags($conn) {
    $tags = [];
    
    $query = "SELECT * FROM tags ORDER BY name ASC";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tags[] = $row;
        }
    }
    
    return $tags;
}

/**
 * Get tags for a post
 * 
 * @param mysqli $conn Database connection
 * @param int $post_id Post ID
 * @return array
 */
function getPostTags($conn, $post_id) {
    $tags = [];
    
    $query = "SELECT t.* FROM tags t 
              JOIN post_tags pt ON t.id = pt.tag_id 
              WHERE pt.post_id = ? 
              ORDER BY t.name ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tags[] = $row;
        }
    }
    
    return $tags;
}

/**
 * Create a URL-friendly slug from a string
 * 
 * @param string $string The string to convert
 * @return string The slug
 */
function createSlug($string) {
    // Replace non letter or digit with -
    $string = preg_replace('~[^\pL\d]+~u', '-', $string);
    // Transliterate
    $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    // Remove unwanted characters
    $string = preg_replace('~[^-\w]+~', '', $string);
    // Trim
    $string = trim($string, '-');
    // Remove duplicate -
    $string = preg_replace('~-+~', '-', $string);
    // Lowercase
    $string = strtolower($string);
    
    if (empty($string)) {
        return 'n-a';
    }
    
    return $string;
}

/**
 * Upload and process an image
 * 
 * @param array $file The uploaded file ($_FILES['image'])
 * @param string $upload_dir The directory to upload to
 * @return array Result with success status and filename or error message
 */
function uploadImage($file, $upload_dir) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Only JPG, PNG and GIF images are allowed'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Image size should be less than 2MB'];
    }
    
    // Create a unique filename
    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $file['name']);
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $upload_path = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'error' => 'Failed to upload image'];
    }
}

/**
 * Create a thumbnail from an image
 * 
 * @param string $source_path Path to the source image
 * @param string $thumb_dir Directory to save the thumbnail
 * @param int $width Thumbnail width
 * @param int $height Thumbnail height
 * @return array Result with success status and filename or error message
 */
function createThumbnail($source_path, $thumb_dir, $width, $height) {
    // Create directory if it doesn't exist
    if (!is_dir($thumb_dir)) {
        mkdir($thumb_dir, 0755, true);
    }
    
    // Get image info
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return ['success' => false, 'error' => 'Invalid image file'];
    }
    
    // Create image from source based on type
    switch ($image_info[2]) {
        case IMAGETYPE_JPEG:
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source_image = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_GIF:
            $source_image = imagecreatefromgif($source_path);
            break;
        default:
            return ['success' => false, 'error' => 'Unsupported image type'];
    }
    
    // Get original dimensions
    $source_width = imagesx($source_image);
    $source_height = imagesy($source_image);
    
    // Calculate thumbnail dimensions while maintaining aspect ratio
    $source_ratio = $source_width / $source_height;
    $thumb_ratio = $width / $height;
    
    if ($source_ratio > $thumb_ratio) {
        // Source image is wider
        $new_height = $height;
        $new_width = $height * $source_ratio;
        $crop_x = ($new_width - $width) / 2;
        $crop_y = 0;
    } else {
        // Source image is taller
        $new_width = $width;
        $new_height = $width / $source_ratio;
        $crop_x = 0;
        $crop_y = ($new_height - $height) / 2;
    }
    
    // Create thumbnail image
    $thumb_image = imagecreatetruecolor($width, $height);
    
    // Preserve transparency for PNG and GIF
    if ($image_info[2] == IMAGETYPE_PNG || $image_info[2] == IMAGETYPE_GIF) {
        imagealphablending($thumb_image, false);
        imagesavealpha($thumb_image, true);
        $transparent = imagecolorallocatealpha($thumb_image, 255, 255, 255, 127);
        imagefilledrectangle($thumb_image, 0, 0, $width, $height, $transparent);
    }
    
    // Resize and crop
    imagecopyresampled(
        $thumb_image, $source_image,
        0, 0, $crop_x, $crop_y,
        $new_width, $new_height,
        $source_width, $source_height
    );
    
    // Generate filename
    $filename = pathinfo($source_path, PATHINFO_FILENAME) . '_thumb.' . pathinfo($source_path, PATHINFO_EXTENSION);
    $thumb_path = $thumb_dir . $filename;
    
    // Save thumbnail
    $success = false;
    switch ($image_info[2]) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($thumb_image, $thumb_path, 90);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($thumb_image, $thumb_path, 9);
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($thumb_image, $thumb_path);
            break;
    }
    
    // Free memory
    imagedestroy($source_image);
    imagedestroy($thumb_image);
    
    if ($success) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'error' => 'Failed to create thumbnail'];
    }
}

/**
 * Sanitize output
 * 
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

