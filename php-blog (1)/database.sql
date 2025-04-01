-- Create database
CREATE DATABASE IF NOT EXISTS dev_blog;
USE dev_blog;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create posts table
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert admin user (password: admin123)
INSERT INTO users (username, password, email) VALUES 
('admin', '$2y$10$8WxmVFNDrgEYxFLs5QXKIeKl7d.VoqP0ZL.bTCe7k3vT3vO8yO4Uy', 'admin@example.com');

-- Insert sample posts
INSERT INTO posts (title, content) VALUES 
('Getting Started with PHP', '<p>PHP is a popular general-purpose scripting language that is especially suited to web development. Fast, flexible and pragmatic, PHP powers everything from your blog to the most popular websites in the world.</p><p>Here''s a simple example:</p><code>&lt;?php<br>echo "Hello, World!";<br>?&gt;</code><p>PHP is a server-side scripting language, which means that it is executed on the server, generating HTML which is then sent to the client. The client would receive the results of running that script, but would not know what the underlying code was.</p>'),
('MySQL Database Basics', '<p>MySQL is an open-source relational database management system. Its name is a combination of "My", the name of co-founder Michael Widenius''s daughter, and "SQL", the abbreviation for Structured Query Language.</p><p>Here''s a basic SQL query:</p><code>SELECT * FROM users WHERE username = ''admin'';</code><p>MySQL is a component of the LAMP web application software stack (and others), which is an acronym for Linux, Apache, MySQL, PHP/Perl/Python.</p>');

