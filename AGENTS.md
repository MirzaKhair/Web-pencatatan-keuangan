# AGENTS.md
# Communication Language

Selalu gunakan Bahasa Indonesia untuk berkomunikasi dengan pengguna.

Gunakan Bahasa Indonesia untuk:

- Menjelaskan rencana kerja.
- Menjelaskan perubahan yang akan dilakukan.
- Menjelaskan file yang dibuat atau diubah.
- Menjelaskan error.
- Memberikan ringkasan setelah pekerjaan selesai.
- Menjawab pertanyaan pengguna.

Gunakan Bahasa Inggris hanya jika diperlukan untuk:

- Nama variabel.
- Nama class.
- Nama function.
- Nama file.
- Perintah terminal.
- Syntax kode.
- Istilah teknis yang lebih tepat dalam Bahasa Inggris.

Jangan menerjemahkan kode, nama file, nama class, nama function, atau perintah terminal.
## Project Overview

Nama aplikasi: Pencatatan Keuangan

Aplikasi web untuk membantu pengguna mencatat pemasukan dan pengeluaran pribadi.

Aplikasi bersifat multi-user. Setiap pengguna hanya dapat melihat dan mengelola data miliknya sendiri.

Fokus versi pertama adalah desktop.

---

# Technology Stack

Gunakan teknologi berikut:

- Laravel
- PHP
- MySQL
- Laravel Blade
- CSS
- JavaScript
- Lucide Icons
- Chart.js untuk grafik laporan

Jangan menggunakan React, Vue, atau framework frontend tambahan kecuali benar-benar diperlukan dan telah disetujui.

Gunakan struktur dan konvensi bawaan Laravel.

---

# Development Principles

Saat mengembangkan proyek:

1. Jangan mengubah struktur database tanpa alasan yang jelas.
2. Jangan membuat fitur di luar spesifikasi tanpa persetujuan.
3. Jangan menghapus fitur yang sudah disepakati.
4. Utamakan kode yang sederhana, jelas, dan mudah dipelajari.
5. Gunakan validasi Laravel untuk semua input penting.
6. Pastikan setiap user hanya dapat mengakses datanya sendiri.
7. Jangan hanya memperbaiki tampilan jika masalah sebenarnya berada pada backend atau database.
8. Sebelum mengubah banyak file, pahami struktur proyek terlebih dahulu.
9. Jelaskan file mana yang dibuat atau diubah setelah menyelesaikan pekerjaan.
10. Jangan melakukan refactor besar yang tidak diminta.
11. Selalu berkomunikasi dengan pengguna menggunakan Bahasa Indonesia.

---

# Application Features

Aplikasi memiliki fitur:

1. Authentication
   - Login
   - Register
   - Logout

2. Setup Saldo Awal

3. Dashboard

4. Transaksi
   - Tambah pemasukan
   - Tambah pengeluaran
   - Melihat transaksi terbaru

5. Kategori
   - Kategori pemasukan
   - Kategori pengeluaran
   - Tambah kategori
   - Edit kategori
   - Hapus kategori jika belum digunakan transaksi

6. Riwayat Transaksi
   - Tabel transaksi
   - Search
   - Filter
   - Edit transaksi
   - Hapus transaksi
   - Pagination

7. Laporan
   - Harian
   - Bulanan
   - Tahunan

8. Pengaturan
   - Ubah nama
   - Ubah email
   - Ubah password

---

# Database Structure

## Table: users

Kolom utama:

- id
- name
- email
- password
- initial_balance
- initial_balance_date
- initial_balance_setup_at
- created_at
- updated_at

Rules:

- initial_balance NULL sebelum setup.
- initial_balance boleh bernilai 0.
- Saldo awal hanya dapat diatur satu kali.
- Saldo awal tidak dapat diubah setelah setup selesai.
- initial_balance_date tidak boleh berada di masa depan.
- initial_balance_setup_at digunakan untuk mengetahui apakah setup saldo awal sudah selesai.

---

## Table: categories

Kolom:

- id
- user_id
- name
- type
- icon
- created_at
- updated_at

Rules:

- type hanya boleh bernilai:
  - income
  - expense
- name wajib diisi.
- icon wajib diisi.
- icon disimpan sebagai nama icon Lucide.
- Jenis kategori tidak dapat diubah setelah kategori dibuat.
- Kategori bawaan dapat diedit.
- Kategori bawaan dapat dihapus jika belum digunakan transaksi.
- Kategori yang sudah digunakan oleh transaksi tidak boleh dihapus.
- Satu user tidak boleh memiliki kategori dengan nama dan type yang sama.

Gunakan unique constraint:

(user_id, name, type)

---

## Table: transactions

Kolom:

- id
- user_id
- category_id
- amount
- transaction_date
- note
- created_at
- updated_at

Rules:

- user_id wajib.
- category_id wajib.
- amount wajib lebih dari 0.
- transaction_date tidak boleh berada di masa depan.
- note bersifat opsional.
- Transaction hanya boleh menggunakan kategori milik user yang sama.

---

# Database Relationships

User memiliki banyak Category.

User memiliki banyak Transaction.

Category memiliki banyak Transaction.

Relationship:

users 1:N categories

users 1:N transactions

categories 1:N transactions

Foreign key rules:

categories.user_id:
ON DELETE CASCADE

transactions.user_id:
ON DELETE CASCADE

transactions.category_id:
ON DELETE RESTRICT

---

# Authentication Flow

## Register

User mengisi:

- Nama
- Email
- Password
- Konfirmasi Password

Setelah register berhasil:

1. User otomatis login.
2. User diarahkan ke halaman Setup Saldo Awal.

---

## Setup Saldo Awal

Halaman:

/setup-initial-balance

User mengisi:

- Saldo awal
- Tanggal saldo awal

Rules:

- Saldo awal >= 0.
- Tanggal tidak boleh di masa depan.
- Setelah berhasil disimpan, saldo awal tidak dapat diubah.
- User yang sudah menyelesaikan setup tidak boleh mengakses setup untuk mengubah data.

Setelah setup berhasil:

1. Simpan saldo awal.
2. Simpan tanggal saldo awal.
3. Simpan initial_balance_setup_at.
4. Buat kategori bawaan.
5. Redirect ke Dashboard.

User yang belum menyelesaikan setup saldo awal tidak boleh mengakses halaman utama aplikasi.

---

# Default Categories

Kategori bawaan dibuat setelah user menyelesaikan setup saldo awal.

## Expense Categories

- Makanan
- Minuman
- Transportasi
- Belanja
- Hiburan
- Pendidikan
- Kesehatan
- Tagihan
- Lainnya

## Income Categories

- Gaji
- Bonus
- Freelance
- Uang Saku
- Investasi
- Lainnya

Setiap kategori harus memiliki icon Lucide yang relevan.

Kategori bawaan adalah kategori biasa.

Tidak perlu menggunakan kolom is_default.

---

# Icon System

Gunakan Lucide Icons secara konsisten.

Database menyimpan nama icon sebagai string.

Contoh:

icon = "utensils"

Jangan menyimpan SVG icon ke database.

Gunakan daftar icon tetap.

Jangan memberikan akses ke seluruh koleksi icon Lucide.

---

# Dashboard

Route:

/dashboard

Dashboard menampilkan:

1. Filter periode
2. Saldo Saat Ini
3. Total Pemasukan
4. Total Pengeluaran
5. Riwayat transaksi terbaru

---

## Dashboard Period Filter

Pilihan:

- Hari Ini
- Minggu Ini
- Bulan Ini
- Tahun Ini
- Semua

Pemasukan dan pengeluaran mengikuti periode yang dipilih.

Riwayat terbaru mengikuti periode yang dipilih.

Saldo Saat Ini tidak mengikuti filter periode.

---

## Current Balance Formula

Saldo Saat Ini dihitung:

Initial Balance
+ Total Income
- Total Expense

Jangan menyimpan current_balance sebagai kolom database.

Saldo dihitung dari data transaksi.

---

# Transactions Page

Route:

/transactions

Halaman digunakan untuk menambahkan transaksi.

Tampilkan:

- Tombol Tambah Pemasukan
- Tombol Tambah Pengeluaran
- Maksimal 5 transaksi terbaru
- Tombol menuju Riwayat

---

## Add Transaction

Tambah transaksi menggunakan modal.

Klik:

Tambah Pemasukan

Maka modal otomatis menggunakan type income.

Klik:

Tambah Pengeluaran

Maka modal otomatis menggunakan type expense.

Field:

- Nominal
- Kategori
- Tanggal
- Catatan

Rules:

- Nominal > 0.
- Kategori wajib.
- Hanya tampilkan kategori sesuai jenis transaksi.
- Default tanggal adalah hari ini.
- Tanggal masa lalu diperbolehkan.
- Tanggal masa depan tidak diperbolehkan.
- Catatan opsional.

Setelah transaksi berhasil disimpan:

1. Tutup modal.
2. Perbarui data transaksi.
3. Tampilkan notifikasi berhasil.

---

# Categories Page

Route:

/categories

Gunakan dua tab:

- Pengeluaran
- Pemasukan

Fitur:

- Tambah kategori menggunakan modal.
- Edit kategori menggunakan modal.
- Hapus kategori dengan konfirmasi.

Jenis kategori tidak dapat diubah setelah kategori dibuat.

Kategori yang sudah digunakan transaksi tidak boleh dihapus.

---

## Category Icons

Gunakan daftar icon tetap.

Contoh icon expense:

- utensils
- cup-soda
- bus
- car
- shopping-cart
- gamepad-2
- graduation-cap
- heart-pulse
- house
- lightbulb
- smartphone
- shirt
- plane
- package

Contoh icon income:

- banknote
- wallet
- briefcase-business
- gift
- trending-up
- landmark
- circle-dollar-sign
- piggy-bank
- coins

Pastikan icon yang digunakan tersedia pada library Lucide yang dipasang.

---

# History Page

Route:

/history

Fokus pada melihat dan mengelola seluruh transaksi.

Tampilan utama menggunakan tabel.

Kolom:

- No
- Tanggal
- Kategori
- Jenis
- Nominal
- Catatan
- Aksi

---

## History Features

### Search

Search berdasarkan:

- Nama kategori
- Catatan transaksi

### Filter

Filter:

- Jenis transaksi
- Kategori
- Periode

Periode:

- Hari Ini
- Minggu Ini
- Bulan Ini
- Tahun Ini
- Semua
- Rentang Tanggal

### Pagination

Gunakan 10 data per halaman.

### Edit

Edit transaksi menggunakan modal.

Rules tetap berlaku:

- Nominal > 0.
- Kategori wajib.
- Tanggal tidak boleh di masa depan.

### Delete

Hapus transaksi harus menggunakan konfirmasi.

Setelah transaksi dihapus, saldo otomatis berubah berdasarkan perhitungan transaksi yang tersisa.

---

# Reports

Route utama:

/reports

Gunakan tab:

- Harian
- Bulanan
- Tahunan

---

## Daily Report

User memilih tanggal.

Tampilkan:

- Saldo awal hari
- Total pemasukan
- Total pengeluaran
- Saldo akhir hari
- Tabel transaksi

Tidak perlu grafik.

Saldo awal hari dihitung berdasarkan saldo awal aplikasi dan seluruh transaksi sebelum tanggal tersebut.

---

## Monthly Report

User memilih:

- Bulan
- Tahun

Tampilkan:

- Saldo awal bulan
- Total pemasukan
- Total pengeluaran
- Saldo akhir bulan

Gunakan grafik:

1. Pemasukan vs Pengeluaran
2. Pengeluaran berdasarkan kategori

Tampilkan tabel ringkasan kategori:

- Kategori
- Jumlah transaksi
- Total

Gunakan Chart.js.

---

## Yearly Report

User memilih tahun.

Tampilkan:

- Saldo awal tahun
- Total pemasukan
- Total pengeluaran
- Saldo akhir tahun

Gunakan grafik:

Pemasukan dan pengeluaran per bulan.

Tampilkan tabel:

- Bulan
- Pemasukan
- Pengeluaran
- Selisih

---

# Report Balance Calculation

Saldo awal periode dihitung dari:

Saldo Awal Aplikasi
+ Semua pemasukan sebelum periode
- Semua pengeluaran sebelum periode

Saldo akhir periode:

Saldo Awal Periode
+ Pemasukan Periode
- Pengeluaran Periode

---

# Settings

Fitur Pengaturan:

- Ubah nama
- Ubah email
- Ubah password

Saldo awal tidak dapat diubah melalui Pengaturan.

---

# UI Layout

Fokus versi pertama:

Desktop.

Gunakan sidebar kiri.

Menu:

- Dashboard
- Transaksi
- Kategori
- Riwayat
- Laporan

Bagian bawah sidebar:

- Pengaturan
- Logout

Gunakan desain yang:

- Bersih
- Modern
- Mudah dibaca
- Tidak terlalu banyak warna
- Konsisten di seluruh halaman

---

# Security and Data Ownership

Setiap user hanya boleh:

- Melihat kategorinya sendiri.
- Mengedit kategorinya sendiri.
- Menghapus kategorinya sendiri.
- Melihat transaksinya sendiri.
- Mengedit transaksinya sendiri.
- Menghapus transaksinya sendiri.

Selalu verifikasi ownership pada controller, query, atau authorization.

Jangan hanya mengandalkan ID dari URL atau request.

---

# Validation Rules

## Initial Balance

- required
- numeric
- min:0

## Transaction Amount

- required
- numeric
- gt:0

## Transaction Date

- required
- date
- before_or_equal:today

## Category Name

- required
- string
- max:100

## Category Type

Hanya:

- income
- expense

---

# Code Organization

Gunakan struktur Laravel yang jelas.

Controller:

app/Http/Controllers/

Models:

app/Models/

Migrations:

database/migrations/

Views:

resources/views/

JavaScript:

Gunakan lokasi yang konsisten sesuai kebutuhan proyek.

CSS:

Gunakan lokasi yang konsisten sesuai konfigurasi Laravel.

Jangan membuat file JavaScript atau CSS acak tanpa struktur yang jelas.

---

# Before Making Changes

Sebelum mengubah kode:

1. Periksa struktur proyek.
2. Pahami file terkait.
3. Identifikasi dampak perubahan.
4. Jangan mengubah file yang tidak berhubungan.

---

# After Completing Work

Setelah menyelesaikan tugas:

1. Jelaskan apa yang dibuat atau diubah.
2. Sebutkan file yang diubah.
3. Jelaskan jika ada migration atau command yang harus dijalankan.
4. Jelaskan cara menguji fitur.
5. Jangan mengklaim fitur berhasil jika belum diperiksa.

---

# Important Restrictions

Jangan:

- Menyimpan current_balance di database.
- Mengizinkan nominal transaksi <= 0.
- Mengizinkan tanggal transaksi di masa depan.
- Mengizinkan kategori tanpa user_id.
- Mengizinkan transaksi menggunakan kategori milik user lain.
- Menghapus kategori yang masih digunakan transaksi.
- Mengubah type kategori setelah kategori dibuat.
- Mengubah saldo awal setelah setup selesai.
- Menambahkan fitur besar di luar spesifikasi tanpa persetujuan.