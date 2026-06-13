# Contributing Guide - SPK TOPSIS

Panduan lengkap untuk berkontribusi pada proyek SPK TOPSIS. Ikuti standar dan konvensi yang ada untuk memastikan kode berkualitas tinggi dan mudah dipelihara.

## 📋 Table of Contents

- [Code Standards](#code-standards)
- [Git Workflow](#git-workflow)
- [Submitting Changes](#submitting-changes)
- [Testing](#testing)
- [Documentation](#documentation)

---

## 🎯 Code Standards

### PHP Coding Style

Proyek ini mengikuti PSR-12 Extended Coding Style dengan beberapa modifikasi lokal.

#### 1. Indentation & Formatting

```php
<?php
// Use 4 spaces for indentation (NOT tabs)
class MyClass {
    private string $property;
    
    public function __construct(string $property) {
        $this->property = $property;
    }
}
```

#### 2. File Header Documentation

Setiap file PHP harus dimulai dengan documentation block:

```php
<?php
/**
 * Brief description of file
 * 
 * Longer description explaining the purpose and usage of this file.
 * Can be multiple lines.
 * 
 * @author Developer Name
 * @version 1.0
 */
```

#### 3. Class Documentation

```php
/**
 * MyClass - Purpose of this class
 * 
 * Longer description of what this class does and when to use it.
 * Include any important notes about usage or behavior.
 * 
 * @author Development Team
 * @version 1.0
 */
class MyClass {
    // ...
}
```

#### 4. Method Documentation

```php
/**
 * Do something useful
 * 
 * Longer description of what this method does, including
 * any side effects or special behavior.
 * 
 * @param string $param1 Description of param1
 * @param int $param2 Description of param2
 * @return bool True if successful, false otherwise
 * @throws InvalidArgumentException If param1 is empty
 * 
 * @example
 * $result = $obj->myMethod('test', 42);
 */
public function myMethod(string $param1, int $param2): bool {
    // Implementation
    return true;
}
```

### Naming Conventions

| Element | Convention | Example |
|---------|-----------|---------|
| **Classes** | PascalCase | `UserModel`, `KaryawanController` |
| **Methods** | camelCase | `getUserById`, `createEmployee` |
| **Properties** | $camelCase | `$userName`, `$isActive` |
| **Constants** | UPPER_SNAKE_CASE | `MAX_UPLOAD_SIZE`, `DEFAULT_ROLE` |
| **Database tables** | snake_case | `user_profiles`, `employee_evaluations` |
| **Database columns** | snake_case | `user_id`, `created_at` |
| **Files** | PascalCase.php | `UserModel.php`, `KaryawanController.php` |
| **Functions** | camelCase | `getUserData()`, `calculateTopsis()` |

### Type Hints

Gunakan type hints untuk semua parameters dan return types:

```php
// Good
public function getUser(int $id): ?array {
    // ...
}

public function saveUser(string $name, string $email, ?string $phone = null): bool {
    // ...
}

// Avoid
public function getUser($id) {
    // ...
}
```

### Access Modifiers

Selalu deklarasikan access modifier untuk properties dan methods:

```php
// Good
private string $username;
protected array $data;
public function getName(): string {
    // ...
}

// Avoid
var $username;  // Ini akan di-interpret sebagai public
function getName() {  // Ini akan public
    // ...
}
```

### Line Length

Keep lines at a reasonable length (max ~100 characters) untuk readability:

```php
// Good
$user = User::where('email', $email)
    ->where('status', 'active')
    ->first();

// Avoid
$user = User::where('email', $email)->where('status', 'active')->where('deleted_at', null)->orderBy('created_at', 'desc')->first();
```

### Comments

Gunakan comments untuk menjelaskan MENGAPA, bukan APA (kode sudah jelas):

```php
// Good
if ($age < 18) {
    // User must be at least 18 to create an account
    throw new InvalidAgeException();
}

// Avoid
// Check if age is less than 18
if ($age < 18) {
    throw new InvalidAgeException();
}

// Very Bad
$x = $y + $z; // Add y and z
```

---

## 📁 Project Structure

### Models (`/models/`)

Model merepresentasikan entitas database dan data access logic:

```php
<?php
class User {
    private \PDO $pdo;
    
    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}
?>
```

**Rules:**
- 1 class per file
- Filename harus match class name (User.php → class User)
- Gunakan type hints
- Validate input
- Handle exceptions

### Controllers (`/controllers/`)

Controller menangani request dan orchestrate antara models dan views:

```php
<?php
class UserController {
    private User $userModel;
    
    public function __construct(\PDO $pdo) {
        $this->userModel = new User($pdo);
    }
    
    public function index(): void {
        $users = $this->userModel->getAll();
        $page_title = 'User List';
        include __DIR__ . '/../views/user/index.php';
    }
}
?>
```

**Rules:**
- 1 class per file
- Filename: `[Name]Controller.php` → class `[Name]Controller`
- Methods return void (output rendered via include)
- Validate user input sebelum passing ke model
- Handle dan display errors gracefully

### Views (`/views/`)

View berisi template HTML dan presentation logic:

```php
<!-- views/user/index.php -->
<?php if (empty($users)): ?>
    <p>No users found.</p>
<?php else: ?>
    <table>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= escape($user['name']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
```

**Rules:**
- Use `escape()` helper untuk output user data (XSS prevention)
- Keep logic minimal, move to controllers/models
- Use PHP short echo tags `<?= ?>`
- Include common layouts (header, footer, sidebar)

---

## 🔒 Security Best Practices

### SQL Injection Prevention

Selalu gunakan prepared statements:

```php
// Good
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);

// Bad
$sql = "SELECT * FROM users WHERE id = $id";
$result = $pdo->query($sql);
```

### XSS Prevention

Escape semua output dari user:

```php
// Good
<p><?= escape($user['name']) ?></p>

// Bad
<p><?= $user['name'] ?></p>
```

### Password Storage

Selalu hash passwords menggunakan PASSWORD_DEFAULT:

```php
// Good
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (password) VALUES (?)");
$stmt->execute([$hash]);

// Bad
$stmt = $pdo->prepare("INSERT INTO users (password) VALUES (?)");
$stmt->execute([$password]);  // Never store plain text!
```

### CSRF Protection

Pertimbangkan menambahkan CSRF tokens untuk forms:

```php
// Generate token
<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

// Verify token
if (!verifyCsrfToken($_POST['csrf_token'])) {
    die('Invalid token');
}
```

---

## 🔄 Git Workflow

### 1. Create Feature Branch

```bash
# Update master
git checkout master
git pull origin master

# Create feature branch
git checkout -b feature/user-management
```

Branch naming convention: `feature/name` atau `fix/description`

### 2. Commit Messages

Write clear, descriptive commit messages:

```
Good:
- Add user authentication system
- Fix employee calculation error
- Refactor TOPSIS calculator for performance

Bad:
- update
- fix bug
- changes
```

Format:
- Use imperative mood ("Add" not "Added")
- Keep first line under 50 characters
- Add detailed description if needed

### 3. Push Changes

```bash
git push origin feature/user-management
```

### 4. Create Pull Request

- Title: Clear description of changes
- Description: Explain what and why
- Link related issues
- Add screenshots for UI changes

---

## 🧪 Testing

### Manual Testing Checklist

Sebelum submit, test:

- [ ] Form validation works
- [ ] Success messages display correctly
- [ ] Error handling works
- [ ] Database operations succeed
- [ ] Authorization checks work
- [ ] No console errors

### Example Test Cases

```php
<?php
// tests/UserTest.php
class UserTest {
    private $pdo;
    private $user;
    
    protected function setUp() {
        $this->pdo = new PDO('sqlite::memory:');
        $this->user = new User($this->pdo);
    }
    
    public function testLogin() {
        // Create test user
        $this->user->create('test', 'password123', 'admin');
        
        // Test login
        $result = $this->user->login('test', 'password123');
        $this->assertNotFalse($result);
    }
}
?>
```

---

## 📚 Documentation

### README

Update README jika:
- Adding new features
- Changing installation steps
- Updating API endpoints
- Menambah dependencies

### Code Comments

Good comments menjelaskan:
- Complex algorithms
- Non-obvious design decisions
- Important warnings or gotchas
- References ke external resources

Avoid:
- Stating obvious (code sudah jelas)
- Outdated information
- Large blocks of commented-out code

### Updating CONTRIBUTING.md

Jika menambah konvensi atau proses baru, update file ini.

---

## 🎓 Development Tips

### Useful Debugging Functions

```php
// Log to error.log
error_log('Debug message: ' . print_r($data, true));

// Display formatted data
echo '<pre>';
print_r($data);
echo '</pre>';

// Check variable type
var_dump($variable);

// Check if variable is set/empty
if (isset($var) && !empty($var)) {
    // Safe to use
}
```

### PHPUnit Testing

```bash
# Install PHPUnit
composer require --dev phpunit/phpunit

# Run tests
./vendor/bin/phpunit tests/
```

### Performance Tips

- Cache database queries when appropriate
- Use indexes on frequently queried columns
- Minimize number of queries in loops
- Use database transactions for multiple operations

---

## ❓ FAQ

**Q: Bagaimana jika saya tidak setuju dengan standar ini?**
A: Diskusikan dengan tim. Konsistensi lebih penting daripada preferensi personal.

**Q: Berapa lama review process?**
A: Biasanya 1-2 hari. Urgent fixes mungkin lebih cepat.

**Q: Bagaimana jika PR saya rejected?**
A: Reviewer akan memberikan feedback. Lakukan perubahan dan resubmit.

**Q: Bisakah saya push directly ke master?**
A: Tidak. Semua changes harus through PR dan di-review.

---

## 📞 Contact

Pertanyaan? Contact tim lead atau buat issue di repository.

---

**Last Updated**: June 2026
**Version**: 1.0
