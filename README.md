<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Vite-7-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
  <img src="https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
</p>

# 🛡️ SecureVault — Secure Document Portal

A **production-ready document management platform** built with Laravel 12, featuring role-based access control, in-browser document previews, dark mode, and zero external dependencies for file conversion.

> **Live Demo:** [securevault-document-portal.onrender.com](https://securevault-document-portal.onrender.com)

---

## ✨ Features

| Feature | Description |
|---|---|
| 🔐 **Role-Based Access** | Two roles — `uploader` (upload + view) and `viewer` (view only) |
| 📄 **Multi-Format Preview** | PDF, DOCX, XLSX, PPTX, TXT — all rendered in-browser |
| 🔄 **PHP-Native Conversion** | Office files converted to HTML using phpoffice |
| 🔗 **Signed URLs** | Document links expire after 30 minutes and can't be shared |
| 🌙 **Dark Mode** | System-wide dark mode with true black backgrounds and high contrast |
| 📱 **Responsive Design** | Mobile-first UI with Tailwind CSS |
| 🛡️ **Sandboxed Previews** | Documents rendered in sandboxed iframes, AJAX-only endpoints |
| ✅ **Client-Side Validation** | File type and size checks before upload (max 20MB) |
| 🔑 **Password Security** | New password can't match current password on reset |

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                        SecureVault Architecture                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│   Browser ──────► Laravel Routes (web.php)                          │
│                       │                                             │
│                       ├── Auth Routes (Breeze)                      │
│                       │     ├── Login / Register                    │
│                       │     ├── Password Reset (read-only email)    │
│                       │     └── Email Verification                  │
│                       │                                             │
│                       ├── Document Routes (signed URLs)             │
│                       │     ├── GET /documents ─────► index         │
│                       │     ├── GET /documents/{id} ► show (signed) │
│                       │     ├── GET /documents/{id}/preview (AJAX)  │
│                       │     ├── GET /upload ─────────► create       │
│                       │     └── POST /documents ─────► store        │
│                       │                                             │
│                       └── Profile Routes                            │
│                             ├── Update Profile                      │
│                             └── Update Password                     │
│                                                                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│   Controllers                    Services                           │
│   ┌──────────────────┐          ┌──────────────────┐                │
│   │ DocumentController│────────►│ DocumentService   │               │
│   │                  │          │  • store()        │                │
│   │  • index()       │          │  • getAll()       │                │
│   │  • create()      │          └──────────────────┘                │
│   │  • store()       │          ┌──────────────────┐                │
│   │  • show()        │────────►│ PreviewService    │                │
│   │  • preview()     │          │  • generatePreview│                │
│   └──────────────────┘          │  • convertDocx    │                │
│                                 │  • convertXlsx    │                │
│   ┌──────────────────┐          │  • convertPptx    │                │
│   │ ProfileController │          │  • getTextContent │                │
│   │ PasswordController│          └──────────────────┘                │
│   │ NewPasswordCtrl   │                                              │
│   └──────────────────┘                                              │
│                                                                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│   Security Layer                                                    │
│   ┌────────────────┐  ┌──────────────┐  ┌───────────────────┐      │
│   │ DocumentPolicy  │  │ RoleMiddleware│  │ Signed URL (30min)│      │
│   │  • viewAny()   │  │  • uploader  │  │  • show route     │      │
│   │  • view()      │  │  • viewer    │  │  • preview route  │      │
│   │  • upload()    │  │              │  │                   │      │
│   │  • preview()   │  │              │  │                   │      │
│   └────────────────┘  └──────────────┘  └───────────────────┘      │
│                                                                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│   Storage (Private)              Database (SQLite)                  │
│   storage/app/private/           ┌──────────────────┐              │
│   ├── documents/ (originals)     │ users (+ role)   │              │
│   └── previews/  (HTML cache)    │ documents        │              │
│                                  │ sessions         │              │
│                                  │ cache            │              │
│                                  └──────────────────┘              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📂 Project Structure

```
securevault/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DocumentController.php    # Document CRUD + preview
│   │   │   ├── ProfileController.php     # User profile management
│   │   │   └── Auth/                     # Breeze auth controllers
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php        # Role-based route guarding
│   │   └── Requests/
│   │       └── StoreDocumentRequest.php  # Upload validation rules
│   ├── Models/
│   │   ├── User.php                      # User model (with role)
│   │   └── Document.php                  # Document model (UUID, mime, paths)
│   ├── Policies/
│   │   └── DocumentPolicy.php           # Authorization rules per role
│   ├── Providers/
│   │   └── AppServiceProvider.php       # HTTPS forcing, policy registration
│   └── Services/
│       ├── DocumentService.php          # File storage & retrieval
│       └── PreviewService.php           # Office → HTML conversion engine
├── config/
│   └── documents.php                    # Allowed types, size limits, paths
├── database/
│   ├── migrations/                      # Schema: users, documents, role column
│   ├── factories/                       # Test factories
│   └── seeders/
│       └── DatabaseSeeder.php           # Demo uploader + viewer accounts
├── resources/
│   ├── css/app.css                      # Design system + dark mode overrides
│   └── views/
│       ├── layouts/                     # app, guest, navigation
│       ├── auth/                        # login, register, reset-password
│       ├── documents/                   # index, show, upload
│       ├── components/                  # Breeze UI components
│       └── vendor/mail/                 # Custom email branding
├── docker/
│   └── start.sh                         # Production startup script
├── Dockerfile                           # Production Docker image
├── docker-compose.yml                   # Local Docker development
└── render.yaml                          # Render deployment blueprint
```

---

## 🔒 Security Features

| Layer | Implementation |
|---|---|
| **Authentication** | Laravel Breeze (login, register, password reset, email verification) |
| **Authorization** | `DocumentPolicy` — role-based (uploader/viewer) |
| **Route Protection** | `RoleMiddleware` restricts upload routes to uploaders only |
| **Signed URLs** | Document view/preview links expire after 30 minutes |
| **AJAX-Only Preview** | Preview endpoint rejects direct browser navigation |
| **Sandboxed Iframes** | Document previews render in sandboxed iframes |
| **Private Storage** | Files stored in `storage/app/private/` (not publicly accessible) |
| **Password Validation** | New password must differ from current password |
| **CSRF Protection** | All forms include CSRF tokens |
| **Input Validation** | Server-side + client-side file type/size validation |

---

## 📄 Supported File Types

| Type | Extension | Preview Method |
|---|---|---|
| PDF | `.pdf` | Native browser rendering via PDF.js |
| Word | `.docx` | Converted to HTML via PhpWord |
| Excel | `.xlsx` | Converted to styled HTML table via PhpSpreadsheet |
| PowerPoint | `.pptx` | Converted to HTML slide cards via PhpPresentation |
| Text | `.txt` | Rendered directly in `<pre>` tag |

> **All Office conversions use pure PHP libraries** — no external binaries, no system dependencies.

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- SQLite

### Local Development

```bash
# Clone the repo
git clone https://github.com/YOUR_USERNAME/securevault-document-portal.git
cd securevault-document-portal

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Create database and seed
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# Build frontend assets
npm run build

# Start the server
php artisan serve
```

Visit `http://localhost:8000` and log in with:

| Email | Password | Role |
|---|---|---|
| `uploader@gmail.com` | `password` | Uploader (can upload + view) |
| `viewer@gmail.com` | `password` | Viewer (can view only) |

### Development with Hot Reload

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server (hot reload for CSS/JS)
npm run dev
```

---

## 🐳 Docker

### Using Docker Compose (Local)

```bash
docker-compose up --build
```

### Manual Docker Build

```bash
docker build -t securevault .
docker run -p 8000:8000 -v securevault-data:/var/data securevault
```

---

## ☁️ Deploy to Render

This project includes a **Render Blueprint** (`render.yaml`) for one-click deployment.

### Steps

1. Push to a GitHub repository
2. Go to [Render Dashboard](https://dashboard.render.com) → **New** → **Blueprint**
3. Connect your GitHub repo — Render auto-detects `render.yaml`
4. Set environment variable `APP_KEY` (run `php artisan key:generate --show` locally)
5. Choose **Starter plan** ($7/mo — required for persistent disk)

### Important Environment Variables

| Variable | Value |
|---|---|
| `APP_KEY` | `base64:...` (generated via artisan) |
| `APP_ENV` | `production` |
| `APP_URL` | `https://your-service.onrender.com` |
| `DB_CONNECTION` | `sqlite` |
| `DB_DATABASE` | `/var/data/database/database.sqlite` |

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run auth tests only
php artisan test --filter=Auth

# Run document tests only
php artisan test --filter=Document
```

**Test coverage includes:** Authentication flows, password reset validation, document policy authorization, document upload/view permissions, and role-based access control.

---

## ⚙️ Configuration

All document-related settings are in `config/documents.php`:

```php
return [
    'allowed_types'      => ['pdf', 'docx', 'pptx', 'xlsx', 'txt'],
    'max_size_kb'        => env('DOCUMENT_MAX_SIZE_KB', 20480), // 20MB
    'storage_path'       => 'private/documents',
    'preview_path'       => 'private/previews',
    'convertible_types'  => ['docx', 'pptx', 'xlsx'],
    'inline_types'       => ['pdf', 'txt'],
    'url_expiry_minutes' => env('DOCUMENT_URL_EXPIRY', 30),
];
```

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 12 (PHP 8.2) |
| **Frontend** | Blade Templates, Tailwind CSS, Alpine.js |
| **Build Tool** | Vite 7 |
| **Database** | SQLite |
| **Auth** | Laravel Breeze |
| **PDF Rendering** | PDF.js |
| **Office Conversion** | phpoffice/phpword, phpspreadsheet, phppresentation |
| **Deployment** | Docker, Render |

