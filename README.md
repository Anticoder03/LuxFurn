# Luxury Furniture Website

A modern, responsive furniture website built with PHP and MySQL.

## Features

- Responsive design
- Product showcase
- Contact form
- About section
- Modern UI/UX

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

## Setup Instructions

1. Create a MySQL database named `furniture_db`

2. Create the products table using the following SQL:

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO products (name, description, price, image_url) VALUES
('Modern Sofa', 'Comfortable 3-seater sofa with premium upholstery', 999.99, 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'),
('Dining Table', 'Elegant wooden dining table seats 6', 799.99, 'https://images.unsplash.com/photo-1617806118233-18e1de247200?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'),
('Bed Frame', 'Queen size bed frame with headboard', 1299.99, 'https://images.unsplash.com/photo-1505693314120-0d443867891c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80');
```

3. Update the database configuration in `config.php` if needed:
   - Default credentials are set for XAMPP (username: "root", password: "")

4. Place all files in your web server's directory (e.g., htdocs for XAMPP)

5. Access the website through your web browser:
   ```
   http://localhost/your-folder-name
   ```

## File Structure

- `index.php` - Main website page
- `config.php` - Database configuration
- `style.css` - Website styles
- `README.md` - This file

## Customization

You can customize the website by:
1. Modifying the colors in `style.css`
2. Adding more products to the database
3. Updating the content in `index.php`
4. Adding more features as needed

## Support

For support or questions, please contact us at info@luxfurn.com 