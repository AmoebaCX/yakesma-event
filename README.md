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
- **Pendaftaran** — form nama/email/WhatsApp + tiket berkode (gratis & berbayar).
  Tersimpan nyata di database ([yakesma-api](../yakesma-api)), bukan cuma di
  browser. Event berbayar pakai **transfer bank manual** — pendaftar dapat
  instruksi transfer + kode referensi, admin mengonfirmasi lewat panel admin.
- **Event Saya** — daftar tiket yang sudah didaftarkan; status disegarkan dari
  server tiap dibuka (mis. begitu admin menandai lunas)
- **Akun** — profil (tersimpan lokal), kontak, Tentang/Privasi/S&K

## Panel Admin

Halaman admin untuk mengelola isi situs & pendaftaran: **`/admin/`**
- Lokal: http://127.0.0.1:5190/admin/
- Live: https://AmoebaCX.github.io/yakesma-event/admin/

Admin punya **dua gerbang masuk terpisah**, sengaja dipisah karena beda tingkat
kepekaan datanya:

1. **Passcode konten** (tab Event/Pengaturan/Pratinjau/Terbitkan) — proteksi
   ringan sisi-browser untuk mengedit konten non-sensitif. Ubah di
   `ADMIN_PASSCODE`, dekat atas `admin/index.html`.
2. **Password Pendaftaran** (tab Pendaftaran) — login sungguhan ke
   [yakesma-api](../yakesma-api) (di-hash scrypt, token sesi 12 jam) karena tab
   ini membaca/mengubah data pribadi pendaftar & status pembayaran nyata.
   Ganti dengan generate hash baru — lihat README `yakesma-api`.

**Tab Event/Pengaturan/Pratinjau/Terbitkan** — kelola event & pengaturan situs
(termasuk rekening bank untuk transfer manual). Alur terbit: tab *Terbitkan* →
**Unduh data.js** (atau *Salin JSON*) → ganti file `data.js` di repo →
`git commit` & `git push` → situs live update ±1 menit. (Atau kirim JSON-nya ke
Claude dan minta "terbitkan".)

**Tab Pendaftaran** — daftar semua pendaftar nyata (bukan simulasi), filter per
status/event, tombol **Tandai Lunas**/**Batalkan** untuk konfirmasi transfer
manual, dan **Ekspor CSV**.

> Konten situs (event, teks, warna) = file **`data.js`** (sumber tunggal),
> dengan fallback bawaan bila gagal dimuat. **Pendaftaran** disimpan di
> database nyata lewat `yakesma-api`, bukan di `data.js` — lihat
> [`../yakesma-api/README.md`](../yakesma-api/README.md).

## Kustomisasi

Cara yang benar: lewat **panel admin** (tab Event/Pengaturan → Terbitkan), bukan
edit manual. Tapi kalau perlu tahu strukturnya:

- **Nama, tagline, kontak, warna, rekening bank** → objek `site` di `data.js`
  (fallback bawaan ada di `_DEFAULT_SITE`, dekat atas `index.html`).
- **Daftar event** → array `events` di `data.js`. Tiap event: `slug, title,
  category, speaker, location, city, online, date, timeStart, timeEnd, price
  (0 = gratis), quota, registered, gradient, emoji, excerpt, about[],
  benefits[], featured`.
- **Logo** → ganti file `yakesma-logo.png` (lockup) & `yakesma-mark.png` (ikon).

## Untuk produksi

CDN Play Tailwind & Babel-in-browser cocok untuk demo/internal. Untuk produksi,
kompilasi Tailwind (Tailwind CLI/PostCSS) dan pre-compile JSX. Versi berbasis
framework juga tersedia di folder `../event-platform` (Next.js) untuk di-build
di mesin yang mendukung native tooling.
