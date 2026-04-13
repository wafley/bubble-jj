# Bubble JJ

**Bubble JJ** is a Laravel-based web module designed to manage and organize **“jedag-jedug” video content**, both for platform administrators and end users.

This project is part of the larger ecosystem of **livetok.online**, serving as a dedicated system for handling video assets, user-generated content, and creative media workflows.

---

## Project Overview

Bubble JJ functions as a **content management subsystem** within the main platform (**livetok.online**). Its primary purpose is to provide structured and efficient management of short-form, high-impact visual content inspired by TikTok trends.

The system supports two main roles:

* **Admin Panel** → Manage all user-generated content across the platform
* **User Panel** → Manage personal video content independently

Built with **Laravel 13**, the project ensures scalability, maintainability, and secure handling of user data and media assets.

---

## Core Objectives

* Centralize management of **jedag-jedug video content**
* Provide a clear separation between **admin and user responsibilities**
* Enable users to **create, manage, and organize their own content**
* Support the main platform (**livetok.online**) with a modular architecture

---

## Key Features

### Admin Features

* Manage all uploaded videos from users
* Moderate and control content visibility
* Edit, update, or delete user content
* Monitor platform activity

### User Features

* Upload and manage personal jedag-jedug videos
* Edit video metadata (title, description, etc.)
* Delete or organize personal content
* Access a personalized dashboard

### System Features

* Laravel 13 MVC architecture
* Centralized configuration for SEO and branding
* Secure authentication and authorization
* Scalable content management structure

---

## Tech Stack

* **Framework**: Laravel 13
* **Backend**: PHP
* **Frontend**: Blade, HTML, CSS, JavaScript
* **Database**: MySQL / MariaDB
* **Server**: Apache / Nginx

---

## Role in Main Platform

Bubble JJ is **not a standalone product**, but a **modular component** of:

* **Main Platform**: livetok.online

It acts as the **content management engine** specifically for:

* Jedag-jedug videos
* User-generated creative assets
* Media organization and moderation

---

## 📂 Project Structure

```bash
app/                # Core application logic (Controllers, Models, Services)
config/             # Configuration files
database/           # Migrations and seeders
public/             # Public entry point and assets
resources/          # Blade templates and frontend resources
routes/             # Web and API routes
storage/            # Logs, cache, and uploaded files
tests/              # Automated tests
```

---

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/wafley/bubble-jj.git
cd bubble-jj
```

### 2. Install Dependencies

```bash
composer update
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Update `.env` file:

```
DB_DATABASE=bubble_jj
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Migration & Seeding:

```bash
php artisan migrate
php spark db:seed DatabaseSeeder
```

### 6. Setup Assets (Important)

Since image assets and frontend libraries are not stored in the Git repository, you need to download them manually:

1. **Download Assets**: Open the following Google Drive link: [`https://drive.google.com/file/d/1vIKmbUJNh-DYwBmz0JwY4vK4fsNemRws/view?usp=sharing`](https://drive.google.com/file/d/1vIKmbUJNh-DYwBmz0JwY4vK4fsNemRws/view?usp=sharing)
2. **Extract Files**: Extract the downloaded `.zip` file.
3. **Placement**: Move/copy the extracted folder into the `public/` directory.
4. Ensure the folder structure looks like this:

```txt

bubble-jj/
├── app/
├── public/
│   ├── assets
│   ├── templates
│   ├── .htaccess
│   ├── favicon.ico
│   ├── index.php
│   └── robots.txt
├── resources/
└── .env

```

### 7. Run Application

```bash
php artisan serve
```

---

## Security

Leveraging Laravel’s built-in security features:

* CSRF Protection
* Authentication & Authorization
* Input Validation
* Secure Session Handling

---

## Project Information

* **Project Name**: Bubble JJ
* **Version**: 2.0
* **Framework**: Laravel 13
* **Author**: Wafley
* **Owner**: PT Digjaya Mahakarya Teknologi
* **Parent Platform**: livetok.online

---

## License

This project is proprietary and maintained by **PT Digjaya Mahakarya Teknologi**.
Unauthorized use, distribution, or modification is strictly prohibited.

---

## Contribution

This project is privately maintained as part of the livetok ecosystem.
For collaboration or integration inquiries, please contact the project owner.

---

## Contact

For business inquiries, partnerships, or technical support, please reach out to the owner.
