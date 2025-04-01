<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Dev Blog</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <section class="page-header">
            <h1>About Us</h1>
        </section>

        <section class="about-content">
            <div class="about-image">
                <img src="assets/images/about-team.jpg" alt="Dev Blog Team">
            </div>
            
            <div class="about-text">
                <h2>Our Mission</h2>
                <p>Dev Blog was created with a simple mission: to provide a platform where developers can share knowledge, experiences, and insights with the global programming community.</p>
                
                <h2>Who We Are</h2>
                <p>We are a team of passionate developers who believe in the power of knowledge sharing. Our diverse backgrounds in web development, mobile applications, data science, and more allow us to cover a wide range of topics relevant to today's tech landscape.</p>
                
                <h2>What We Do</h2>
                <p>Through our blog, we aim to:</p>
                <ul>
                    <li>Share practical tutorials and guides</li>
                    <li>Discuss emerging technologies and trends</li>
                    <li>Provide insights into best practices</li>
                    <li>Create a supportive community for developers at all levels</li>
                </ul>
                
                <h2>Our Values</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <h3>Quality</h3>
                        <p>We prioritize accuracy and depth in all our content, ensuring readers receive reliable information.</p>
                    </div>
                    <div class="value-card">
                        <h3>Accessibility</h3>
                        <p>We believe knowledge should be accessible to everyone, regardless of their experience level.</p>
                    </div>
                    <div class="value-card">
                        <h3>Community</h3>
                        <p>We foster an inclusive environment where developers can learn from each other.</p>
                    </div>
                    <div class="value-card">
                        <h3>Innovation</h3>
                        <p>We stay at the forefront of technology to bring you insights on the latest developments.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="team-section">
            <h2>Meet Our Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="assets/images/team-member1.jpg" alt="John Doe">
                    <h3>John Doe</h3>
                    <p class="role">Founder & Lead Developer</p>
                    <p>Full-stack developer with 10+ years of experience in PHP, JavaScript, and cloud technologies.</p>
                </div>
                <div class="team-member">
                    <img src="assets/images/team-member2.jpg" alt="Jane Smith">
                    <h3>Jane Smith</h3>
                    <p class="role">Content Director</p>
                    <p>Technical writer and developer with a passion for making complex concepts accessible.</p>
                </div>
                <div class="team-member">
                    <img src="assets/images/team-member3.jpg" alt="Mike Johnson">
                    <h3>Mike Johnson</h3>
                    <p class="role">UX/UI Specialist</p>
                    <p>Designer and front-end developer focused on creating beautiful, user-friendly interfaces.</p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>

