# FlipPlay — Modul 2 dilanjutkan ke Demo Modul 3

Folder `src/` mempertahankan frontend FlipPlay dari project Modul 2. Folder `backend/` berisi prototype backend PHP OOP untuk demo terminal Modul 3.

## Frontend Modul 2
- `src/index.html`: beranda dan form kontak.
- `src/vibe-grid.html`: halaman katalog Vibe Grid.
- `src/js/script.js`: navbar, validasi form, pencarian judul, filter aura, dan pemanggilan Kitsu API dengan Jikan API sebagai cadangan.
- `src/main.css` dan `src/output.css`: styling Tailwind.

Jalankan frontend seperti sebelumnya:
1. `npm install`
2. `npm run dev`
3. Buka `src/index.html` dengan Live Server.

## Backend demo Modul 3
Backend ini adalah simulasi terminal PHP OOP yang melanjutkan konsep katalog dan filter Vibe Grid. Karena ketentuan Modul 3 meminta simulasi command line, data contoh disiapkan lokal dan tidak melakukan request ke Kitsu/Jikan. Frontend Modul 2 tetap menggunakan alur API yang sudah ada.

Jalankan dari root project:
```bash
php backend/index.php
```

## Struktur backend
```text
backend/
├── index.php
└── src/
    ├── Models/
    │   ├── Media.php
    │   ├── Anime.php
    │   └── User.php
    └── Services/
        └── MediaLibrary.php
```

## Pemetaan ketentuan Modul 3
1. Minimal 3 class: `Media`, `Anime`, `User`, `MediaLibrary`.
2. Constructor: terdapat `__construct()` pada setiap class.
3. Property dan method: data serta perilaku dikelola dalam class.
4. Access modifier: property `private`/`protected`, method yang perlu dipakai dari luar `public`.
5. Inheritance: `Anime extends Media`.
6. Type hinting dan return type: misalnya `addMedia(Media $media): bool`, `searchByTitle(string $keyword): array`, `findById(int $id): ?Media`.
7. Namespace: `FlipPlay\\Models` dan `FlipPlay\\Services`.
8. Entry point: `backend/index.php`.

## Simulasi
- Menambahkan media ke library.
- Menampilkan daftar media.
- Mencari judul.
- Menampilkan detail media.
- Filter High Energy berdasarkan aturan Vibe Grid Modul 2.
- Menampilkan koleksi pengguna.

Catatan: prototype belum memakai database dan belum menghubungkan PHP ke JavaScript frontend. Itu tidak diminta oleh ketentuan demo terminal pada Modul 3.
