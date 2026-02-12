# คู่มือการติดตั้งระบบทุนการศึกษา

## ข้อกำหนดของระบบ (Requirements)

- PHP 8.x (แนะนำ 8.5+)
- MySQL 8.x หรือ MariaDB 10.x
- Node.js 14.x ขึ้นไป
- Composer
- npm

## วิธีการติดตั้ง

### 1. Clone โปรเจ็ค

```bash
git clone https://github.com/Piti9427/Final_Project.git
cd Final_Project
git checkout develop  # ใช้ develop branch ที่มีการแก้ไขล่าสุด
```

### 2. ติดตั้ง Dependencies

```bash
# PHP Dependencies
composer install

# Node.js Dependencies (ใช้ --legacy-peer-deps สำหรับความเข้ากันได้)
npm install --legacy-peer-deps
```

### 3. ตั้งค่า Database

#### 3.1 สร้าง Database

```bash
mysql -u root -p -e "CREATE DATABASE newcompany CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

หรือถ้าไม่มี password:

```bash
mysql -u root -e "CREATE DATABASE newcompany CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

#### 3.2 Import ข้อมูล

```bash
mysql -u root -p newcompany < newcompany.sql
```

หรือถ้าไม่มี password:

```bash
mysql -u root newcompany < newcompany.sql
```

#### 3.3 ตั้งค่า MySQL สำหรับ PHP 8.x

```bash
mysql -u root -e "SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';"
```

#### 3.4 ตรวจสอบว่า Import สำเร็จ

```bash
mysql -u root -p -e "USE newcompany; SHOW TABLES;"
```

ควรเห็นตารางทั้งหมด 11 ตาราง:
- assessments
- authorize
- company_images
- company_info
- inventory
- logos
- press_release
- scholarship_applications
- scholarships
- suppliers
- users

### 4. ตั้งค่าการเชื่อมต่อ Database

แก้ไขไฟล์ `config.php` ให้ตรงกับการตั้งค่า MySQL ของคุณ:

```php
<?php
$servername = "localhost";
$username = "root";
$password = "";  // ใส่รหัสผ่าน MySQL ของคุณ
$dbname = "newcompany";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
```

### 5. รันเว็บเซิร์ฟเวอร์

```bash
php -S localhost:8080
```

### 6. เปิดเบราว์เซอร์

ไปที่: `http://localhost:8080`

## ข้อมูลการ Login

### Admin
- Username: `admin`
- Password: `123456`

### อาจารย์
- Username: `aaa`, `bbb`, `ccc`, `ddd`, `eee`, `fff`
- Password: `123456`

### นักศึกษา
- Username: `user`, `user1`
- Password: `123456`

## การแก้ปัญหา (Troubleshooting)

### ปัญหา: MySQL GROUP BY error

**Error:** `Expression #8 of SELECT list is not in GROUP BY clause`

**วิธีแก้:**
```bash
mysql -u root -e "SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';"
```

### ปัญหา: Table 'newcompany.users' doesn't exist

**วิธีแก้:**
1. ลบ database เดิมและสร้างใหม่
```bash
mysql -u root -p -e "DROP DATABASE IF EXISTS newcompany; CREATE DATABASE newcompany CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

2. Import ไฟล์ SQL อีกครั้ง
```bash
mysql -u root -p newcompany < newcompany.sql
```

### ปัญหา: npm install error (node-sass)

**Error:** `Node-gyp failed to build your package`

**วิธีแก้:**
```bash
npm install --legacy-peer-deps
```

### ปัญหา: Port 8000 ใช้ไม่ได้

**วิธีแก้:**
```bash
php -S localhost:8080
# หรือ port อื่นๆ
php -S localhost:3000
```

## โครงสร้าง Git

- `main`: Production branch
- `develop`: Development branch (แนะนำให้ clone และใช้ branch นี้)
- `feature/*`: Feature branches

## คุณสมบัติของระบบ

- ระบบจัดการทุนการศึกษา
- ระบบประเมินผลการขอทุน
- ระบบจัดการผู้ใช้และสิทธิ์
- รายงาน PDF
- ระบบ Login แยกตามบทบาท (Admin, อาจารย์, นักศึกษา)

## Deploy บน Cloud

ต้องการ deploy ให้คนอื่นเข้าใช้งานได้เลย? ดูคู่มือที่ [RAILWAY.md](RAILWAY.md)

## ติดต่อ

หากมีปัญหาหรือข้อสงสัย กรุณาติดต่อผู้พัฒนาระบบ
