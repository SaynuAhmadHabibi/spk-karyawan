# API Documentation - SPK TOPSIS

Dokumentasi lengkap endpoint dan cara penggunaan API SPK TOPSIS.

## 📋 Overview

SPK TOPSIS menggunakan routing berbasis URL parameter. Semua request diarahkan ke `index.php` dengan parameter `act` untuk action.

**Format URL:**
```
index.php?act=ACTION&sub=SUBACTION&id=RESOURCE_ID
```

**Method:**
- GET: Untuk display form atau fetch data
- POST: Untuk submit form atau process data

---

## 🔐 Authentication

### Login
```
GET/POST  index.php?act=login
```

**POST Parameters:**
- `username` (string): Username akun
- `password` (string): Password akun

**Response:**
- Success: Redirect ke dashboard
- Failure: Display login form dengan error message

**Example:**
```html
<form method="POST" action="index.php?act=login">
    <input type="text" name="username" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
```

### Logout
```
GET  index.php?act=logout
```

**Response:**
- Destroys session
- Redirect ke login page

---

## 📊 Dashboard

### Dashboard Home
```
GET  index.php?act=dashboard
```

**Authorization:** Requires login

**Response:** Dashboard view dengan summary data

---

## 👥 Karyawan (Employee Management)

### List Employees
```
GET  index.php?act=karyawan
```

**Authorization:** All authenticated users

**Response:** List semua karyawan (aktif + nonaktif)

### Create Employee Form
```
GET  index.php?act=karyawan&sub=create
```

**Authorization:** admin, manager only

**Response:** Form untuk input karyawan baru

### Create Employee
```
POST  index.php?act=karyawan&sub=create
```

**POST Parameters:**
- `nama` (string, required): Nama karyawan
- `jabatan` (string): Jabatan
- `divisi` (string): Divisi
- `tanggal_masuk` (date): Tanggal masuk (YYYY-MM-DD)
- `status` (string): Status (aktif/nonaktif)

**Response:**
- Success: Redirect ke list dengan success message
- Failure: Display form dengan error message

**Example:**
```bash
curl -X POST "http://localhost/spk-topsis/index.php?act=karyawan&sub=create" \
  -d "nama=Budi&jabatan=Manager&divisi=IT&status=aktif"
```

### Edit Employee Form
```
GET  index.php?act=karyawan&sub=edit&id=EMPLOYEE_ID
```

**Parameters:**
- `id` (int): Employee ID

**Authorization:** admin, manager only

**Response:** Form pre-filled dengan data karyawan

### Update Employee
```
POST  index.php?act=karyawan&sub=edit&id=EMPLOYEE_ID
```

**Parameters:**
- `id` (int): Employee ID

**POST Parameters:** Same as create

**Response:** Redirect ke list dengan success message

### Delete Employee
```
GET  index.php?act=karyawan&sub=delete&id=EMPLOYEE_ID
```

**Parameters:**
- `id` (int): Employee ID

**Authorization:** admin only

**Response:** Redirect ke list dengan success/error message

---

## 📋 Kriteria (Evaluation Criteria)

### List Criteria
```
GET  index.php?act=kriteria
```

**Response:** List semua kriteria evaluasi

### Create Criteria Form
```
GET  index.php?act=kriteria&sub=create
```

### Create Criteria
```
POST  index.php?act=kriteria&sub=create
```

**POST Parameters:**
- `nama_kriteria` (string): Nama kriteria
- `bobot` (float): Bobot/weight (0-100)
- `atribut` (string): benefit atau cost
- `min_value` (float): Nilai minimum
- `max_value` (float): Nilai maksimum

### Edit Criteria
```
GET  index.php?act=kriteria&sub=edit&id=CRITERIA_ID
POST index.php?act=kriteria&sub=edit&id=CRITERIA_ID
```

### Delete Criteria
```
GET  index.php?act=kriteria&sub=delete&id=CRITERIA_ID
```

---

## ⭐ Penilaian (Employee Evaluation)

### Input Evaluation Form
```
GET  index.php?act=penilaian_input
```

**Response:** Form untuk input penilaian karyawan

### Save Evaluation
```
POST  index.php?act=penilaian_input
```

**POST Parameters:**
```
periode=2024-06-01
karyawan[1][kriteria][1]=85
karyawan[1][kriteria][2]=90
karyawan[2][kriteria][1]=75
...
```

**Response:** Redirect dengan success message

### Evaluation History
```
GET  index.php?act=penilaian_history
```

**Response:** List periode penilaian

### Edit Period Evaluation
```
GET  index.php?act=penilaian_edit&periode=2024-06-01
POST index.php?act=penilaian_edit&periode=2024-06-01
```

### Delete Period Evaluation
```
GET  index.php?act=penilaian_delete&periode=2024-06-01
```

---

## 🎯 TOPSIS Analysis

### Reward Analysis Form
```
GET  index.php?act=hitung_reward_form
```

**Response:** Form untuk pilih periode TOPSIS Reward

### Calculate Reward TOPSIS
```
POST  index.php?act=hitung_reward
```

**POST Parameters:**
- `periode` (string): Period untuk calculate

**Response:** Redirect ke hasil

### View Reward Results
```
GET  index.php?act=hasil_reward
```

**Response:** Hasil ranking reward employees

### Punishment Analysis Form
```
GET  index.php?act=hitung_punishment_form
```

### Calculate Punishment TOPSIS
```
POST  index.php?act=hitung_punishment
```

### View Punishment Results
```
GET  index.php?act=hasil_punishment
```

### Calculation Details
```
GET  index.php?act=detail_perhitungan&tipe=reward
```

**Parameters:**
- `tipe` (string): reward atau punishment

**Response:** Detail langkah-langkah perhitungan TOPSIS

---

## 📄 Reports (Laporan)

### Export to Excel
```
GET  index.php?act=export_excel&tipe=reward
```

**Parameters:**
- `tipe` (string): reward atau punishment

**Response:** Excel file download

**Example:**
```
GET  index.php?act=export_excel&tipe=reward
```

### Export to PDF
```
GET  index.php?act=export_pdf&tipe=reward
```

**Parameters:**
- `tipe` (string): reward atau punishment

**Response:** PDF file download

---

## 👤 User Management

### List Users
```
GET  index.php?act=user
```

**Authorization:** admin only

**Response:** List semua users

### Create User
```
POST  index.php?act=user_store
```

**POST Parameters:**
- `username` (string): Username
- `password` (string): Password
- `role` (string): admin, manager, direktur

### Update User
```
POST  index.php?act=user_update
```

**POST Parameters:**
- `id` (int): User ID
- `username` (string): Username
- `role` (string): Role
- `password` (string, optional): New password

### Delete User
```
GET  index.php?act=user_delete&id=USER_ID
```

**Parameters:**
- `id` (int): User ID

---

## 🧑‍💼 Profile

### View Profile
```
GET  index.php?act=profil
```

**Response:** User profile page

### Upload Photo
```
POST  index.php?act=profil_upload_photo
```

**POST Parameters:**
- `photo` (file): Image file

### Change Password
```
POST  index.php?act=profil_change_password
```

**POST Parameters:**
- `old_password` (string): Password lama
- `new_password` (string): Password baru
- `confirm_password` (string): Konfirmasi password

---

## 📊 Response Format

### Success Response
```
HTTP/1.1 200 OK
Location: index.php?act=karyawan
Set-Cookie: PHPSESSID=...

// Session message:
$_SESSION['success'] = 'Operation berhasil'
```

### Error Response
```
HTTP/1.1 200 OK (masih 200, tapi dengan error message)

// Session message:
$_SESSION['error'] = 'Deskripsi error'
```

---

## 🔑 Status Codes

| Code | Meaning |
|------|---------|
| 200 | Success (OK) |
| 302 | Redirect (setelah POST success) |
| 403 | Forbidden (no permission) |
| 404 | Not Found |

---

## 🚀 Usage Examples

### Get Employee Data
```bash
# List employees
curl "http://localhost/spk-topsis/index.php?act=karyawan"

# Get specific employee (view page)
curl "http://localhost/spk-topsis/index.php?act=karyawan&sub=edit&id=1"
```

### Create New Employee
```bash
curl -X POST "http://localhost/spk-topsis/index.php?act=karyawan&sub=create" \
  -d "nama=Ahmad+Rizki&jabatan=Developer&divisi=IT&tanggal_masuk=2024-01-01&status=aktif"
```

### Calculate TOPSIS
```bash
curl -X POST "http://localhost/spk-topsis/index.php?act=hitung_reward" \
  -d "periode=2024-06-01"
```

### Export Results
```bash
# Excel
curl "http://localhost/spk-topsis/index.php?act=export_excel&tipe=reward" \
  > hasil_reward.xlsx

# PDF
curl "http://localhost/spk-topsis/index.php?act=export_pdf&tipe=punishment" \
  > hasil_punishment.pdf
```

---

## 🔒 Authentication & Authorization

Semua endpoint kecuali login require authenticated session.

**Roles & Permissions:**

| Action | Admin | Manager | Direktur |
|--------|-------|---------|----------|
| View employees | ✓ | ✓ | ✓ |
| Create employee | ✓ | ✓ | ✗ |
| Edit employee | ✓ | ✓ | ✗ |
| Delete employee | ✓ | ✗ | ✗ |
| Input penilaian | ✓ | ✓ | ✗ |
| View TOPSIS hasil | ✓ | ✓ | ✓ |
| Export report | ✓ | ✓ | ✓ |
| Manage users | ✓ | ✗ | ✗ |

---

## 🐛 Error Handling

Aplikasi menggunakan session messages untuk error handling:

```php
// Success
$_SESSION['success'] = 'Operation berhasil';

// Error
$_SESSION['error'] = 'Terjadi kesalahan';

// Warning
$_SESSION['warning'] = 'Warning message';
```

Messages di-display di view dan di-clear setelah display.

---

## 📝 Notes

- Semua date format: YYYY-MM-DD
- Semua timestamps: YYYY-MM-DD HH:MM:SS
- Currency: Rupiah (IDR)
- Language: Indonesian
- Timezone: Asia/Jakarta

---

**Last Updated:** June 2026
**Version:** 1.0
