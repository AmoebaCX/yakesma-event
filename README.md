# YAKESMA — Platform Event

Website pendaftaran event YAKESMA (Lembaga Amil Zakat Nasional), terinspirasi
dari struktur saforiginal.id. Dibuat sebagai **satu file `index.html`** dengan
React + Tailwind + Babel via CDN — **tanpa proses build**, langsung jalan.

## Menjalankan

```bash
node server.js
```

Lalu buka http://127.0.0.1:5190

(Atau ganti port: `PORT=8080 node server.js`.)

> Catatan: file di-render langsung di browser (React/Babel/Tailwind dari CDN),
> jadi butuh koneksi internet saat pertama membuka.

## Fitur

- **Beranda** — hero, kategori, daftar "Event Terbaru"
- **Event** — semua event + pencarian & filter kategori
- **Detail event** — poster, jadwal, lokasi, kuota, deskripsi, benefit, pemateri
- **Pendaftaran** — form nama/email/WhatsApp + tiket berkode (gratis & berbayar,
  pembayaran disimulasikan). Tersimpan di browser (localStorage).
- **Event Saya** — daftar tiket yang sudah didaftarkan
- **Akun** — profil (tersimpan lokal), kontak, Tentang/Privasi/S&K

## Panel Admin

Halaman admin untuk mengelola isi situs (tanpa ngoding): **`/admin/`**
- Lokal: http://127.0.0.1:5190/admin/
- Live: https://AmoebaCX.github.io/yakesma-event/admin/
- **Passcode default:** `yakesma2026` (ubah di `ADMIN_PASSCODE`, dekat atas `admin/index.html`).

Fitur: tambah/edit/hapus/urutkan event, atur brand (nama, tagline, kontak, warna),
**pratinjau langsung**, lalu **Terbitkan**.

**Alur terbit:** admin → tab *Terbitkan* → **Unduh data.js** (atau *Salin JSON*) →
ganti file `data.js` di repo → `git commit` & `git push` → situs live update ±1 menit.
(Atau kirim JSON-nya ke Claude dan minta "terbitkan".)

> Konten situs = file **`data.js`** (sumber tunggal). `index.html` memakainya, dengan
> fallback bawaan bila `data.js` gagal dimuat. Passcode admin hanya proteksi ringan
> sisi-browser — isi live tetap aman karena hanya berubah lewat push ke repo.

## Kustomisasi (semua di dalam `index.html`)

- **Nama, tagline, kontak, WhatsApp, Instagram, alamat** → objek `SITE` di bagian
  "KONFIGURASI BRAND".
- **Warna brand** → `tailwind.config` di `<head>` (hijau `#28a840`, hijau tua
  `#185838`, emas `#f6b700`, oranye `#f58634`).
- **Daftar event** → array `EVENTS`. Tiap event: `slug, title, category, speaker,
  location, city, online, date, timeStart, timeEnd, price (0 = gratis), quota,
  registered, gradient, emoji, excerpt, about[], benefits[], featured`.
- **Logo** → ganti file `yakesma-logo.png` (lockup) & `yakesma-mark.png` (ikon).

## Untuk produksi

CDN Play Tailwind & Babel-in-browser cocok untuk demo/internal. Untuk produksi,
kompilasi Tailwind (Tailwind CLI/PostCSS) dan pre-compile JSX. Versi berbasis
framework juga tersedia di folder `../event-platform` (Next.js) untuk di-build
di mesin yang mendukung native tooling.
