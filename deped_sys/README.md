# DepEd Zamboanga City Division - Information & Content Management System

A centralized, full-stack Information and Content Management System designed to manage public advisories, official issuances, dynamic web pages, and educational content for the Department of Education (DepEd) Zamboanga City Division.

---

## 🚀 Tech Stack & Core Packages

* **Backend:** Laravel v12 (PHP)
* **Frontend:** Tailwind CSS, Alpine.js, Blade Templating
* **Database:** MySQL / MariaDB
* **Page Builder:** SortableJS (Drag-and-drop) & CKEditor (Rich-text)
* **Table Management:** Yajra DataTables (Server-side data rendering)
* **File Processing:** PhpSpreadsheet (CSV/XLSX imports and exports)
* **QR Code Generation:** SimpleSoftwareIO Simple QrCode

---

## 📋 Prerequisites

Before setting up the project locally or on a server, ensure you have the following installed:
* **Git** (Version control)
* **XAMPP / Web Server** (Apache, PHP 8.2+, MySQL)
* **Composer** (PHP dependency manager)
* **Node.js & NPM** (for frontend assets)

---

## 🛠️ Local Installation & Setup

Follow these steps to get the project running in your local development environment:

**1. Clone the repository**
```bash
git clone <your-repository-url>
cd deped_sys
```

**2. Install PHP Dependencies**
```bash
composer install
```

**3. Install Required Composer Packages**
```bash
composer require yajra/laravel-datatables-oracle
composer require phpoffice/phpspreadsheet
composer require simplesoftwareio/simple-qrcode
```

**4. Install Frontend Dependencies**
```bash
npm install
```
**5. Configure Environment Variables**
Copy the example environment file and update it with your local settings:
```bash
cp .env.example .env
```

Open `.env` and update the following values:
```env
APP_NAME="DepEd Zamboanga Portal"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=memo_advis
DB_USERNAME=root
DB_PASSWORD=
```

**6. Generate Application Key**
```bash
php artisan key:generate
```

**7. Create the Database**
Create a new MySQL database matching the `DB_DATABASE` value you set in `.env`:
```sql
CREATE DATABASE memo_advis;
```

**8. Run Database Migrations**
```bash
php artisan migrate
```

**9. Seed the Database**
Populate the database with default roles and an initial Super Admin account:
```bash
php artisan db:seed
```

**10. Create Storage Symlink**
This allows uploaded files (banner images, PDF issuances, etc.) to be publicly accessible:
```bash
php artisan storage:link
```

**11. Build Frontend Assets**
For development (with hot reloading):
```bash
npm run dev
```

For production:
```bash
npm run build
```

**12. Start the Local Development Server**
```bash
php artisan serve
```

The application will be available at `http://localhost:8000.`

---

## 👥 User Roles

The system implements strict role-based access control, with three distinct roles tailored for specific administrative and content management functions:

| Role | Description |
|---|---|
| **Super Admin** | Full system access. Manages all user accounts, global system settings, dynamic page layouts, and access controls. |
| **Information Office** | Manages public-facing web content. Responsible for updating banners, building dynamic pages, and publishing public advisories. |
| **Issuance Manager** | Dedicated role for document control. Strictly handles the uploading, categorization, and management of official DepEd memoranda, advisories, and procurement documents. |

---

### Default Administrator Credentials

After seeding, log in with the default admin account:

```text
Email:    admin@example.com
Password: 12345
```
⚠️ **Change these credentials immediately after your first login in a production environment.**

---

## 📁 Bulk Data Processing

The system supports mass uploading and exporting of records (such as Issuances and Procurement data) via `.csv` or `.xlsx` spreadsheet files.

1. Download the provided spreadsheet template from the Admin dashboard.
2. Fill in the required fields (Title, Category, Date, Reference Number, etc.).
3. Upload the completed file. The system will automatically:
   - Import and categorize the records.
   - Resolve or flag duplicate reference numbers.
   - Update the public-facing data tables immediately.


---

## ✉️ Mail Configuration (Optional but Recommended)

To enable automated email notifications (such as sending account credentials to newly created users or handling password resets), you must configure your mail driver in the `.env` file:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=systemdeped@gmail.com
MAIL_PASSWORD=bzumthkteeywuzyf
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="systemdeped@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🚢 Production Deployment

When deploying to a live Ubuntu/Linux production server, run the following commands after pulling the latest changes:

```bash
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

Ensure your web server (Apache/Nginx) points its document root to the /public directory of the project.

Important Server Permissions
To prevent "Permission Denied" errors when admins upload PDF issuances or banner images, grant your web server ownership of the cache and storage folders:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

PHP.ini Upload Limits
To allow the uploading of high-resolution banners and large PDF documents, ensure your server's php.ini file is configured properly. Find and adjust these lines:
```Ini, TOML
upload_max_filesize = 25M
post_max_size = 30M
```

(Remember to restart Apache/Nginx after modifying php.ini)

### Sample Nginx Configuration

```ngix
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/html/deped-portal/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🧪 Running Tests

```bash
php artisan test
```

---

## 🤝 Contributing & Credits
This project was developed by Jose Nolan Gamaliel B. Iglesia as part of a web development internship, in collaboration with the DepEd Zamboanga City Division IT team. For bug reports or feature suggestions, please open an issue on the repository or contact the development team directly.

---

## 📄 License
This project is proprietary software developed for the Department of Education – Zamboanga City Division. Unauthorized distribution or commercial use is prohibited without express written consent from the authors and DepEd Zamboanga City Division.