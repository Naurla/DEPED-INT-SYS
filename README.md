# 🏫 DepEd Zamboanga City Division — Information & Content Management System

A centralized, full-stack Information and Content Management System designed to manage public advisories, official issuances, dynamic web pages, and educational content for the **Department of Education (DepEd) Zamboanga City Division**.

---

## 📑 Table of Contents

- [Tech Stack](#-tech-stack--core-packages)
- [Features](#-features)
- [Prerequisites](#-prerequisites)
- [Local Installation & Setup](#️-local-installation--setup)
- [User Roles](#-user-roles)
- [Default Administrator Credentials](#-default-administrator-credentials)
- [Activity Logs](#-activity-logs)
- [Bulk Data Processing](#-bulk-data-processing)
- [Mail Configuration](#-mail-configuration-optional-but-recommended)
- [Production Deployment](#-production-deployment)
- [Running Tests](#-running-tests)
- [Contributing & Credits](#-contributing--credits)
- [License](#-license)

---

## 🚀 Tech Stack & Core Packages

| Layer | Technology |
|---|---|
| **Backend** | Laravel v12 (PHP 8.2+) |
| **Frontend** | Tailwind CSS, Alpine.js, Blade Templating |
| **Database** | MySQL / MariaDB |
| **Page Builder** | SortableJS (Drag-and-drop) & CKEditor (Rich-text) |
| **Table Management** | Yajra DataTables (Server-side rendering) |
| **File Processing** | PhpSpreadsheet (CSV/XLSX import & export) |
| **QR Code Generation** | SimpleSoftwareIO Simple QrCode |
| **Activity Logging** | Spatie Laravel Activitylog |

---

## ✨ Features

- **Public Advisory Management** — Publish, update, and archive public-facing advisories and announcements.
- **Official Issuances & Document Control** — Upload and categorize DepEd memoranda, advisories, and procurement documents with reference number tracking.
- **Dynamic Page Builder** — Drag-and-drop page layout builder with rich-text editing (CKEditor) for Information Office staff.
- **Banner Management** — Upload and manage homepage and section banners.
- **Bulk Data Import/Export** — Mass upload or download records via `.csv` / `.xlsx` spreadsheet files.
- **QR Code Generation** — Auto-generate QR codes for issuances and public documents.
- **Role-Based Access Control** — Three distinct roles (Super Admin, Information Office, Issuance Manager) with strictly scoped permissions.
- **Activity Logs** — Full audit trail of all user actions across the system, powered by Spatie Laravel Activitylog.
- **User Account Management** — Super Admin can create accounts and send credentials via email notifications.

---

## 📋 Prerequisites

Before setting up the project locally or on a server, ensure you have the following installed:

- **Git** — Version control
- **XAMPP / Web Server** — Apache, PHP 8.2+, MySQL
- **Composer** — PHP dependency manager
- **Node.js & NPM** — For frontend asset compilation

---

## 🛠️ Local Installation & Setup

Follow these steps to get the project running in your local development environment:

**1. Clone the repository**
```bash
git clone https://github.com/Naurla/DEPED-INT-SYS.git
cd DEPED-INT-SYS
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
composer require spatie/laravel-activitylog
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

Create a new MySQL database matching the `DB_DATABASE` value in your `.env`:
```sql
CREATE DATABASE memo_advis;
```

**8. Publish Activity Log Migration**

Before running migrations, publish the Spatie activitylog migration:
```bash
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
```

**9. Run Database Migrations**
```bash
php artisan migrate
```

**10. Seed the Database**

Populate the database with default roles and an initial Super Admin account:
```bash
php artisan db:seed
```

**11. Create Storage Symlink**

This allows uploaded files (banner images, PDF issuances, etc.) to be publicly accessible:
```bash
php artisan storage:link
```

**12. Build Frontend Assets**

For development (with hot reloading):
```bash
npm run dev
```

For production:
```bash
npm run build
```

**13. Start the Local Development Server**
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`.

---

## 👥 User Roles

The system implements strict role-based access control with three distinct roles tailored for specific administrative and content management functions:

| Role | Description |
|---|---|
| **Super Admin** | Full system access. Manages all user accounts, global system settings, dynamic page layouts, activity logs, and access controls. |
| **Information Office** | Manages public-facing web content. Responsible for updating banners, building dynamic pages, and publishing public advisories. |
| **Issuance Manager** | Dedicated role for document control. Strictly handles the uploading, categorization, and management of official DepEd memoranda, advisories, and procurement documents. |

---

## 🔑 Default Administrator Credentials

After seeding, log in with the default admin account:

```
Email:    admin@example.com
Password: 12345
```

> ⚠️ **Change these credentials immediately after your first login in a production environment.**

---

## 📋 Activity Logs

The system uses the **[Spatie Laravel Activitylog](https://spatie.be/docs/laravel-activitylog)** package to maintain a full audit trail of all significant user actions — including logins, content creation, updates, deletions, and document uploads.

### What Gets Logged

| Action | Description |
|---|---|
| **Login / Logout** | Records when and which user authenticated or signed out |
| **Content Published** | Tracks creation or publishing of advisories and pages |
| **Issuances Uploaded** | Logs document uploads with reference numbers |
| **Records Updated** | Captures before/after values when records are modified |
| **Records Deleted** | Preserves a record of deletions and who performed them |
| **User Management** | Tracks account creation, role changes, and password resets |

### Viewing the Activity Log

Activity logs are accessible to the **Super Admin** via the Admin Dashboard under the **Activity Logs** section. Logs display:
- The **causer** (which user performed the action)
- The **subject** (which record was affected)
- A **description** of the event
- The **timestamp** of the activity

### How It Works (Technical Reference)

The package stores all activity in the `activity_log` database table. Activity can be logged manually anywhere in the application:

```php
// Basic usage
activity()->log('Published a new advisory');

// Advanced usage — log with subject and causer
activity()
    ->performedOn($issuance)
    ->causedBy(auth()->user())
    ->withProperties(['title' => $issuance->title])
    ->log('Uploaded a new issuance');
```

Models that support automatic event logging use the `LogsActivity` trait:

```php
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Issuance extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'category', 'reference_number'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
```

> For full documentation, visit [spatie.be/docs/laravel-activitylog](https://spatie.be/docs/laravel-activitylog/introduction).

---

## 📁 Bulk Data Processing

The system supports mass uploading and exporting of records (such as Issuances and Procurement data) via `.csv` or `.xlsx` spreadsheet files.

1. Download the provided spreadsheet template from the Admin Dashboard.
2. Fill in the required fields (Title, Category, Date, Reference Number, etc.).
3. Upload the completed file. The system will automatically:
   - Import and categorize the records.
   - Resolve or flag duplicate reference numbers.
   - Update the public-facing data tables immediately.

---

## ✉️ Mail Configuration (Optional but Recommended)

To enable automated email notifications (such as sending account credentials to newly created users or handling password resets), configure your mail driver in the `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@deped-zamboanga.edu.ph
MAIL_FROM_NAME="DepEd Zamboanga Portal"
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

> Ensure your web server (Apache/Nginx) points its document root to the `/public` directory of the project.

### Server Permissions

To prevent "Permission Denied" errors when admins upload PDF issuances or banner images, grant your web server ownership of the cache and storage folders:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### PHP.ini Upload Limits

To allow uploading of high-resolution banners and large PDF documents, adjust these values in your server's `php.ini`:

```ini
upload_max_filesize = 25M
post_max_size = 30M
```

> Remember to restart Apache/Nginx after modifying `php.ini`.

### Sample Nginx Configuration

```nginx
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

This project was developed by **Joebert Sintoy** and **Jose Nolan Iglesia** as part of a web development internship, in collaboration with the **DepEd Zamboanga City Division IT team**.

For bug reports or feature suggestions, please open an issue on the repository or contact the development team directly.

---

## 📄 License

This project is **proprietary software** developed for the Department of Education – Zamboanga City Division. Unauthorized distribution or commercial use is prohibited without express written consent from the authors and DepEd Zamboanga City Division.
