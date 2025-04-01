<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

// Process contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // In a real application, you would send an email here
        // For this example, we'll just simulate success
        $success = 'Thank you for your message! We will get back to you soon.';
        
        // Clear form data
        $name = '';
        $email = '';
        $subject = '';
        $message = '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Dev Blog</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <section class="page-header">
            <h1>Contact Us</h1>
        </section>

        <section class="contact-section">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <p>Have a question, suggestion, or just want to say hello? We'd love to hear from you!</p>
                
                <div class="contact-details">
                    <div class="contact-item">
                        <h3>Email</h3>
                        <p><a href="mailto:info@devblog.com">info@devblog.com</a></p>
                    </div>
                    <div class="contact-item">
                        <h3>Follow Us</h3>
                        <div class="social-links">
                            <a href="#" class="social-link">Twitter</a>
                            <a href="#" class="social-link">GitHub</a>
                            <a href="#" class="social-link">LinkedIn</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <h3>Office</h3>
                        <p>123 Tech Street<br>San Francisco, CA 94107<br>United States</p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-container">
                <h2>Send Us a Message</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" class="contact-form">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="6" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </section>
        
        <section class="map-section">
            <h2>Find Us</h2>
            <div class="map-container">
                <!-- In a real application, you would embed a Google Map or similar here -->
                <div class="map-placeholder">
                    <img src="assets/images/map-placeholder.jpg" alt="Map">
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>

