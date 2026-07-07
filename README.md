# Bincom Election Management System

A PHP-based election result management system built for the Bincom PHP Developer Internship technical assessment.

## Tech Stack

- **PHP 8+** with PDO for database operations
- **MySQL** database
- **Bootstrap 5** for responsive UI
- **HTML5**, **CSS3**, **JavaScript**
- **MVC** architecture pattern

## Features

### Question 1: Polling Unit Result
- Dropdown selection of polling units
- Displays polling unit name, number, ward, and LGA
- Shows party scores in a formatted Bootstrap table

### Question 2: LGA Summed Result
- Dropdown selection of Local Government Areas
- Calculates total scores by joining: LGA → Ward → Polling Unit → `announced_pu_results`
- Does NOT use `announced_lga_results` table (manual calculation)
- Displays total polling units counted per LGA

### Question 3: Add New Result
- Complete form for new polling unit information
- Dynamic ward dropdown (AJAX based on LGA selection)
- Input fields for all political party scores
- Database transactions (both inserts succeed or fail together)
- Full validation with error messages

### Bonus Features
- Search polling units
- Dashboard with summary statistics
- Responsive mobile design
- Bootstrap Icons throughout
- Breadcrumb navigation
- Loading indicators
- Auto-dismissing alerts

## Project Structure

```
bincom-election/
├── config/
│   └── database.php          # PDO database connection
├── controllers/
│   └── PollingController.php  # Business logic & request handling
├── models/
│   └── PollingModel.php       # Database queries (data access layer)
├── views/
│   ├── home.php               # Dashboard page
│   ├── polling-result.php     # Question 1: Polling unit result
│   ├── lga-result.php         # Question 2: LGA summed result
│   └── add-result.php         # Question 3: Add new result
├── assets/
│   ├── css/
│   │   └── style.css          # Custom styles
│   └── js/
│       └── main.js            # Custom JavaScript
├── includes/
│   ├── header.php             # HTML head & opening tags
│   ├── navbar.php             # Navigation bar
│   └── footer.php             # Footer & closing tags
├── database/
│   └── bincom_test.sql        # Database schema with sample data
├── index.php                  # Application entry point
├── routes.php                 # URL routing
└── README.md                  # This file
```

## Setup Instructions

### 1. Import the Database

1. Open phpMyAdmin or MySQL command line
2. Create a new database or use existing:

```sql
CREATE DATABASE bincom_test;
```

3. Import the provided SQL file:

```bash
mysql -u root -p bincom_test < database/bincom_test.sql
```

Or use phpMyAdmin:
- Select `bincom_test` database
- Click "Import"
- Choose `database/bincom_test.sql`
- Click "Go"

### 2. Configure Database Connection

Edit `config/database.php` and update credentials if needed:

```php
private string $host = 'localhost';
private string $dbname = 'bincom_test';
private string $username = 'root';
private string $password = '';
```

### 3. Running the Application

#### XAMPP (Windows)
1. Copy the `bincom-election/` folder to `C:\xampp\htdocs\`
2. Start Apache and MySQL via XAMPP Control Panel
3. Open browser and visit: `http://localhost/bincom-election/`

#### WAMP (Windows)
1. Copy the `bincom-election/` folder to `C:\wamp64\www\`
2. Start WAMP services
3. Open browser and visit: `http://localhost/bincom-election/`

#### LAMP (Linux)
1. Copy the `bincom-election/` folder to `/var/www/html/`
2. Ensure proper permissions:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/bincom-election/
   ```
3. Restart Apache: `sudo systemctl restart apache2`
4. Open browser and visit: `http://localhost/bincom-election/`

## Key SQL Queries

### Question 1 - Polling Unit Result
```sql
SELECT party_abbreviation, party_score
FROM announced_pu_results
WHERE polling_unit_uniqueid = :polling_unit_id
ORDER BY party_score DESC;
```

### Question 2 - LGA Summed Result
```sql
SELECT apr.party_abbreviation, SUM(apr.party_score) AS total_score
FROM lga l
JOIN ward w ON l.uniqueid = w.lga_id
JOIN polling_unit pu ON w.uniqueid = pu.ward_id
JOIN announced_pu_results apr ON pu.uniqueid = apr.polling_unit_uniqueid
WHERE l.uniqueid = :lga_id
GROUP BY apr.party_abbreviation
ORDER BY total_score DESC;
```

### Question 3 - Insert Polling Unit
```sql
INSERT INTO polling_unit (...) VALUES (...);
INSERT INTO announced_pu_results (...) VALUES (...);
```

## Security Features

- All database queries use PDO prepared statements (prevents SQL injection)
- Input validation (numeric scores, required fields)
- XSS prevention via `htmlspecialchars()`
- Database transactions for atomic operations
- Error handling with try-catch blocks
