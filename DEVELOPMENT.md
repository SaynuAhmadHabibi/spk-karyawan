# Development Guide - SPK TOPSIS

Panduan untuk setup development environment dan common development tasks.

## 🛠️ Development Environment Setup

### Prerequisites

- **PHP** 7.4 atau lebih tinggi
- **MySQL** 5.7 atau lebih tinggi
- **Composer** (recommended)
- **Git**
- Text editor atau IDE (VS Code, PHPStorm, dll)

### Installation Steps

#### 1. Clone Repository

```bash
cd c:\xampp\htdocs
git clone <repository-url> spk-topsis
cd spk-topsis
```

#### 2. Install Dependencies

```bash
composer install
```

Atau jika tidak menggunakan composer, skip step ini.

#### 3. Configure Database

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'spk_topsis');
define('DB_USER', 'root');
define('DB_PASS', '');
```

#### 4. Start Web Server

```bash
# XAMPP
start xampp-control.exe  # Windows
./xampp/mampp  # Linux/Mac

# Or use PHP built-in server
php -S localhost:8000
```

#### 5. Access Application

```
http://localhost/spk-topsis/
```

Login dengan:
- Username: `admin`
- Password: `admin`

---

## 📁 Project Structure Explained

```
spk-topsis/
├── config/
│   └── database.php        # Database connection & auto-setup
├── controllers/            # Application logic
│   ├── AuthController.php
│   ├── KaryawanController.php
│   ├── KriteriaController.php
│   ├── PenilaianController.php
│   ├── TopsisController.php
│   └── ...
├── models/                 # Data access layer
│   ├── User.php
│   ├── Karyawan.php
│   ├── Kriteria.php
│   ├── Penilaian.php
│   ├── TopsisCalculator.php
│   └── ...
├── views/                  # Templates (HTML/PHP)
│   ├── auth/
│   ├── karyawan/
│   ├── kriteria/
│   ├── penilaian/
│   ├── topsis/
│   ├── laporan/
│   ├── user/
│   ├── layouts/
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── sidebar.php
│   └── ...
├── lib/                    # Helper functions & utilities
│   ├── Constants.php       # Application constants
│   ├── Helper.php          # Helper functions
│   ├── Router.php          # Routing helper
│   └── ...
├── assets/                 # Static files
│   ├── css/
│   ├── js/
│   ├── img/
│   └── uploads/
├── scripts/                # Database setup & utility scripts
├── tests/                  # Unit tests
├── vendor/                 # Composer dependencies
├── index.php               # Application entry point
├── README.md               # Project documentation
├── CONTRIBUTING.md         # Contribution guidelines
├── API.md                  # API documentation
└── DEVELOPMENT.md          # This file
```

---

## 💡 Common Development Tasks

### Adding a New Feature

#### 1. Create Database Table (if needed)

Edit `config/database.php` dan tambahkan CREATE TABLE statement:

```php
$pdo->exec("CREATE TABLE IF NOT EXISTS `feature_table` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
```

#### 2. Create Model

```bash
# File: models/Feature.php
```

```php
<?php
/**
 * Feature - Handles feature data
 */
class Feature {
    private \PDO $pdo;
    
    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM feature_table");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
```

#### 3. Create Controller

```bash
# File: controllers/FeatureController.php
```

```php
<?php
/**
 * FeatureController - Handles feature requests
 */
require_once __DIR__ . '/../models/Feature.php';

class FeatureController {
    private Feature $featureModel;
    
    public function __construct(\PDO $pdo) {
        $this->featureModel = new Feature($pdo);
    }
    
    public function index(): void {
        $features = $this->featureModel->getAll();
        $page_title = 'Features';
        include __DIR__ . '/../views/feature/index.php';
    }
}
?>
```

#### 4. Create View

```bash
# File: views/feature/index.php
```

```php
<h1><?= $page_title ?></h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($features as $feature): ?>
        <tr>
            <td><?= $feature['id'] ?></td>
            <td><?= escape($feature['name']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

#### 5. Register Route

Edit `index.php` dan tambahkan case untuk feature:

```php
case 'feature':
    require_once 'controllers/FeatureController.php';
    $ctrl = new FeatureController($pdo);
    $ctrl->index();
    break;
```

#### 6. Test

```
http://localhost/spk-topsis/index.php?act=feature
```

---

### Debugging

#### 1. Enable Error Reporting

```php
// Add to config/database.php atau awal file
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

#### 2. Log Errors

```php
// Use helper function
logError('Something went wrong', 'Context info');

// Or use error_log
error_log('Debug: ' . print_r($data, true));
```

#### 3. Use var_dump

```php
// Display variable info
echo '<pre>';
var_dump($variable);
echo '</pre>';
```

#### 4. Check Session Data

```php
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
```

#### 5. Database Query Debugging

```php
// Check if query succeeded
if (!$stmt->execute($params)) {
    echo 'Error: ' . print_r($stmt->errorInfo(), true);
}
```

---

### Testing Locally

#### Manual Testing

1. Test login/logout
2. Test CRUD operations
3. Test form validation
4. Test authorization (roles)
5. Test error handling
6. Check browser console untuk JavaScript errors

#### Using Browser DevTools

```
F12 → Network tab untuk check requests
F12 → Console tab untuk JavaScript errors
```

---

### Database Debugging

#### Check Database

```bash
# Connect ke MySQL
mysql -u root -p

# Select database
USE spk_topsis;

# List tables
SHOW TABLES;

# Check table structure
DESCRIBE karyawan;

# View data
SELECT * FROM karyawan LIMIT 10;
```

#### Reset Database

```bash
# Delete database
DROP DATABASE spk_topsis;

# Access app - database akan di-create otomatis
```

---

## 🚀 Optimization Tips

### Database

```php
// Use pagination untuk large datasets
$offset = ($page - 1) * 20;
$stmt = $pdo->prepare("SELECT * FROM table LIMIT ? OFFSET ?");
$stmt->execute([20, $offset]);

// Cache frequently accessed data
if (!isset($_SESSION['cache_kriteria'])) {
    $_SESSION['cache_kriteria'] = $model->getAll();
}

// Use indexes pada frequently queried columns
CREATE INDEX idx_status ON karyawan(status);
```

### PHP

```php
// Use type hints untuk performance
public function getUser(int $id): ?array {
    // Type checking happens at compile time
}

// Avoid repeated array lookups
$user_role = $_SESSION['user']['role'] ?? '';
$role = $user_role;  // Instead of accessing $_SESSION multiple times
```

### Frontend

```php
// Minify CSS/JS in production
<link rel="stylesheet" href="assets/css/style.min.css">

// Use caching headers
header('Cache-Control: max-age=31536000');
```

---

## 📊 Code Quality Tools

### Suggested Tools

#### Code Style
```bash
# PHP CodeSniffer
composer require --dev squizlabs/php_codesniffer

# Run
./vendor/bin/phpcs src/
```

#### Static Analysis
```bash
# PHPStan
composer require --dev phpstan/phpstan

# Run
./vendor/bin/phpstan analyse models/ controllers/
```

#### Testing
```bash
# PHPUnit
composer require --dev phpunit/phpunit

# Create test
tests/UserTest.php

# Run
./vendor/bin/phpunit
```

---

## 📝 Development Checklist

Sebelum commit/submit:

- [ ] Code mengikuti coding standards
- [ ] Comments jelas dan up-to-date
- [ ] No hardcoded values (gunakan constants)
- [ ] Error handling implemented
- [ ] Input validation done
- [ ] Database queries use prepared statements
- [ ] Output escaped (prevent XSS)
- [ ] Access control checked
- [ ] No console errors
- [ ] Manual testing passed
- [ ] Database can be reset & recreated

---

## 🎯 Performance Profiling

### Measure Execution Time

```php
$start = microtime(true);

// Code to profile
$results = $model->getAll();

$end = microtime(true);
echo "Execution time: " . ($end - $start) . " seconds";
```

### Database Query Performance

```php
// Use EXPLAIN untuk see query plan
EXPLAIN SELECT * FROM karyawan WHERE status = 'aktif';

// Add indexes untuk slow queries
ALTER TABLE karyawan ADD INDEX idx_status (status);
```

---

## 🔧 Troubleshooting

### White Screen of Death

```php
// Enable error display to see actual error
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### 404 Not Found

- Check if route is registered in index.php
- Check if controller file exists
- Check controller filename matches class name

### Database Connection Error

- Verify MySQL is running
- Check credentials in config/database.php
- Check database user has proper permissions

### Session Not Working

- Check session_start() is called
- Check cookies enabled in browser
- Check session folder writable

### File Not Found in include

```php
// Use __DIR__ untuk relative paths
include __DIR__ . '/../models/User.php';

// Instead of
include 'models/User.php';  // Relative to current working directory
```

---

## 📚 Learning Resources

### PHP
- [PHP Official Documentation](https://www.php.net/manual/en/)
- [PHP Best Practices](https://www.phptherightway.com/)

### TOPSIS Method
- [TOPSIS Algorithm Explained](https://en.wikipedia.org/wiki/TOPSIS)
- [Mathematical Formula](https://www.sciencedirect.com/science/article/pii/S1877050915001659)

### Security
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)

---

## 🤝 Getting Help

1. Check existing code for similar patterns
2. Read documentation (README, CONTRIBUTING)
3. Check error logs
4. Ask team members
5. Search online for common issues

---

**Last Updated:** June 2026
**Version:** 1.0
