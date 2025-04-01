# DevBlog - A Blogging Website with Admin Panel  

DevBlog is a simple and lightweight blogging platform built with PHP and MySQL. It features an admin panel for managing posts, tags, and users, making it easy to maintain your blog.  

## Features  
- ✅ Create, edit, and delete blog posts  
- ✅ Manage tags for better content organization  
- ✅ Secure admin panel for content management  
- ✅ Simple and clean UI  

## Installation  

### 1. Clone the Repository  
```
git clone https://github.com/yourusername/devblog.git
cd devblog
```

### 2. Import Database  
- Import the provided SQL file (`database.sql`) into your MySQL database.  

### 3. Configure Database Connection  
- Update your database credentials in `config/database.php`:  
```
define('DB_HOST', 'your_host');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database_name');
```

### 4. Run the Application  
- Start your local server (Apache + MySQL).  
- Open the project in your browser.  

## Admin Login  
- **Username:** `admin`  
- **Password:** `admin123`  

## Contributing  
Feel free to fork this repository and contribute!  

## License  
This project is open-source and available under the MIT License.  
