# Deploy บน Railway.app

## ขั้นตอนการ Deploy

### 1. สร้างบัญชี Railway
1. ไปที่ https://railway.app/
2. Sign up ด้วย GitHub account
3. Verify email

### 2. สร้าง Project ใหม่

1. คลิก **"New Project"**
2. เลือก **"Deploy from GitHub repo"**
3. เลือก repository ของโปรเจ็คนี้
4. Railway จะ detect PHP และ deploy อัตโนมัติ

### 3. เพิ่ม MySQL Database

1. ใน Project คลิก **"+ New"**
2. เลือก **"Database"** → **"Add MySQL"**
3. Railway จะสร้าง MySQL instance ให้อัตโนมัติ

### 4. Import ข้อมูลเข้า Database

#### วิธีที่ 1: ใช้ Railway Web UI (ง่ายที่สุด)

1. ไปที่ Railway dashboard
2. คลิกที่ MySQL service
3. คลิก **"Connect"** → **"MySQL Shell"**
4. ใน shell ให้รัน:
```sql
CREATE DATABASE newcompany CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE newcompany;
-- คัดลอกเนื้อหาจาก newcompany.sql และวางที่นี่
```

#### วิธีที่ 2: ใช้ Railway CLI (ถ้าติดตั้งได้)

```bash
# ใช้ npx แทนการติดตั้ง global
npx @railway/cli login

# Link project
npx @railway/cli link

# เชื่อมต่อกับ MySQL
npx @railway/cli connect MySQL

# Import ข้อมูล (ใน MySQL shell)
source newcompany.sql;
```

#### วิธีที่ 3: ใช้ MySQL Client ภายใน

1. ไปที่ MySQL service ใน Railway
2. คลิก **"Connect"** → คัดลอก connection string
3. ใช้ MySQL client เชื่อมต่อ:

```bash
mysql -h <host> -u root -p<password> -P <port> railway
```

4. Import ไฟล์:
```sql
CREATE DATABASE newcompany CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE newcompany;
source newcompany.sql;
```

### 5. ตั้งค่า Environment Variables

1. ไปที่ PHP service (ไม่ใช่ MySQL)
2. คลิก **"Variables"** tab
3. คลิก **"+ New Variable"** และเพิ่ม:

```
MYSQL_HOST=<คัดลอกจาก MySQL service>
MYSQL_USER=root
MYSQL_PASSWORD=<คัดลอกจาก MySQL service>
MYSQL_DATABASE=newcompany
MYSQL_PORT=<คัดลอกจาก MySQL service>
```

**หรือใช้ Reference Variables (ง่ายกว่า):**

1. คลิก **"+ New Variable"**
2. เลือก **"Add Reference"**
3. เลือก MySQL service
4. เลือกตัวแปรที่ต้องการ:
   - `MYSQLHOST` → ตั้งชื่อเป็น `MYSQL_HOST`
   - `MYSQLUSER` → ตั้งชื่อเป็น `MYSQL_USER`
   - `MYSQLPASSWORD` → ตั้งชื่อเป็น `MYSQL_PASSWORD`
   - `MYSQLDATABASE` → ตั้งชื่อเป็น `MYSQL_DATABASE`
   - `MYSQLPORT` → ตั้งชื่อเป็น `MYSQL_PORT`

### 6. Redeploy

1. คลิก **"Deploy"** → **"Redeploy"**
2. รอให้ deploy เสร็จ
3. คลิก **"View Logs"** เพื่อดู deployment status

### 7. เปิดเว็บไซต์

1. ไปที่ **"Settings"** tab
2. ใน **"Domains"** section คลิก **"Generate Domain"**
3. Railway จะสร้าง URL ให้ เช่น `your-app.up.railway.app`
4. เปิด URL นั้นในเบราว์เซอร์

## ตรวจสอบการทำงาน

### ดู Logs
```bash
railway logs
```

### เชื่อมต่อ MySQL
```bash
railway connect MySQL
```

### ดู Environment Variables
```bash
railway variables
```

## ข้อมูลการ Login

เหมือนกับ local:
- **Admin**: username: `admin`, password: `12345678912345678900`

## Troubleshooting

### ปัญหา: Connection failed

**วิธีแก้:**
1. ตรวจสอบว่า Environment Variables ถูกต้อง
2. ตรวจสอบว่า MySQL service กำลังทำงาน
3. ดู logs: `railway logs`

### ปัญหา: Table doesn't exist

**วิธีแก้:**
1. เชื่อมต่อกับ MySQL: `railway connect MySQL`
2. ตรวจสอบว่ามีตาราง: `SHOW TABLES;`
3. ถ้าไม่มี ให้ import ใหม่: `source newcompany.sql;`

### ปัญหา: 502 Bad Gateway

**วิธีแก้:**
1. ตรวจสอบ logs
2. ตรวจสอบว่า `nixpacks.toml` ถูกต้อง
3. Redeploy

## ค่าใช้จ่าย

Railway มี free tier:
- $5 credit ฟรีทุกเดือน
- เพียงพอสำหรับ hobby project
- ไม่ต้องใส่บัตรเครดิต (แต่ใส่จะได้ credit เพิ่ม)

## ข้อดีของ Railway

✅ Deploy ง่าย ไม่ต้อง config เยอะ
✅ รองรับ PHP + MySQL
✅ Auto-deploy เมื่อ push GitHub
✅ มี free tier
✅ มี CLI ใช้งานง่าย
✅ มี logs และ monitoring

## ทางเลือกอื่น

ถ้า Railway ไม่เหมาะ ลองดู:
- **Render.com** - คล้าย Railway
- **Fly.io** - ใช้ Docker
- **DigitalOcean App Platform** - $5/เดือน
- **Shared Hosting** - Hostinger, InfinityFree (ฟรี)

## ติดต่อ

หากมีปัญหา:
1. ดู Railway docs: https://docs.railway.app/
2. Railway Discord: https://discord.gg/railway
3. ติดต่อผู้พัฒนาระบบ
