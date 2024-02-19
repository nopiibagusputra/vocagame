# Assessment VocaGame

Repositori Project Test

## Fitur User (Admin Level)

1. Menambah data User
2. Edit data User
3. Hapus data User
4. Update data User

## Fitur Product (Admin Level)

1. Menambah data Product
2. Edit data Product
3. Hapus data Product
4. Update data Product

## Fitur Wallet

1. Menambah Wallet User (Admin Level)
2. TopUp Wallet User (Admin Level)
3. Withdrawal Wallet User (Admin Level)

## Fitur Transaksi

1. Menambah Transaksi Produk (User Level)

## Framework Aplikasi

1. Backend | Laravel 9.52.16 (PHP 8.2.15)
2. Database | MySQL

## Instalasi dan Konfigurasi Database

1. Buat Database dengan nama vocagame

## Instalasi dan Konfigurasi Backend

1. Clone repositori:

    ```bash
    git clone https://github.com/nopiibagusputra/vocagame.git
    ```

2. Masuk ke folder backend:

    ```bash
    cd vocagame
    ```

3. Instal dependensi aplikasi dengan Composer:

    ```bash
    composer install
    ```

4. Salin file `.env.example` menjadi `.env`:

    ```bash
    cp .env.example .env
    ```

5. Update file `.env`. kemudian ganti nama database menjadi `vocagame`.

6. Jalankan generate key

    ```bash
    php artisan key:generate
    ```

7. Jalankan optimisasi aplikasi

    ```bash
    php artisan optimize
    ```

8. Jalankan migrasi database beserta seeder data dummy:
    admin Level
    "email": "admin@admin.com",
    "password": "admin"

    User Level
    "email": "toddi@galuh.com",
    "password": "toddi"

    User Level
    "email": "bima@shindu.com",
    "password": "bima"

    ```bash
    php artisan migrate --seed
    ```

10. Jalankan server pengembangan Laravel:

    ```bash
    php artisan serve
    ```

API backend akan dapat diakses pada `http://127.0.0.1:8000/api/v1`.

Aplikasi sudah dapat digunakan.

##
