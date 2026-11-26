# Sistem Servis Barang Elektronik (Projek UAS PBWL)

Paket kode sumber lengkap (Migrations, Models, Seeders, Form Requests, Policies, Controllers, RESTful API Resources, Vue 3 + Inertia.js Pages, dan konfigurasi Serverless Vercel) sesuai seluruh materi **Modul 1 sampai Modul 7 PBWL**.

## Cara Membuka dan Menjalankan di VS Code

1. **Buat Project Laravel 13 Baru** di terminal VS Code:
   ```bash
   composer create-project laravel/laravel:^13.0 sistem-servis-elektronik
   cd sistem-servis-elektronik
   ```
2. **Install Package Pendukung (Modul 4, 5, 7):**
   ```bash
   composer require laravel/breeze --dev
   php artisan breeze:install vue
   composer require spatie/laravel-permission
   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
   php artisan install:api
   ```
3. **Ekstrak File ZIP Ini ke Folder Project:**
   Salin semua folder (`app`, `api`, `database`, `resources`, `routes`, `vercel.json`) ke dalam folder `sistem-servis-elektronik` (pilih *Replace / Timpa* jika diminta).
4. **Daftarkan Middleware di `bootstrap/app.php`:**
   Pastikan `bootstrap/app.php` mendaftarkan alias Spatie (`role`, `permission`) dan `ForceJsonResponse` pada grup `api`:
   ```php
   ->withMiddleware(function (Middleware $middleware) {
       $middleware->alias([
           'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
           'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
       ]);
       $middleware->api(prepend: [
           \App\Http\Middleware\ForceJsonResponse::class,
       ]);
   })
   ```
5. **Jalankan Migrasi, Seeder, Build Frontend & Server:**
   ```bash
   php artisan migrate:fresh --seed
   npm install
   npm run build
   php artisan serve
   ```

## Akun Uji Bawaan Seeder
* **Admin:** `admin@servis.test` | Password: `password`
* **Teknisi:** `teknisi@servis.test` | Password: `password`
* **Pelanggan:** `pelanggan@servis.test` | Password: `password`
* **Kode Tiket Demo (Lacak Publik):** `SRV-DEMO01`
