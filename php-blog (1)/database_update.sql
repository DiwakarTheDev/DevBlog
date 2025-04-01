-- Add tags table
CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add post_tags relationship table
CREATE TABLE IF NOT EXISTS post_tags (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Add some sample tags
INSERT INTO tags (name, slug) VALUES 
('PHP', 'php'),
('MySQL', 'mysql'),
('JavaScript', 'javascript'),
('CSS', 'css'),
('HTML', 'html'),
('Web Development', 'web-development');

-- Add tags to existing posts
INSERT INTO post_tags (post_id, tag_id) VALUES 
(1, 1), -- PHP post with PHP tag
(1, 6), -- PHP post with Web Development tag
(2, 2), -- MySQL post with MySQL tag
(2, 6); -- MySQL post with Web Development tag

-- Modify posts table to add thumbnail field
ALTER TABLE posts ADD COLUMN thumbnail VARCHAR(255) AFTER image;

