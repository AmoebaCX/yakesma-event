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

Satu gerbang masuk untuk seluruh admin: login sungguhan ke
[yakesma-api](../yakesma-api) (password di-hash scrypt, token sesi 12 jam) —
bukan lagi passcode terpisah di sisi-browser, karena semua tab (termasuk
Event/Pengaturan) sekarang membaca & menulis konten situs yang sungguhan tayang
publik, bukan draft lokal. Ganti password dengan generate hash baru — lihat
README `yakesma-api`.

**Tab Event/Pengaturan/Pratinjau** — kelola event & pengaturan situs (termasuk
rekening bank untuk transfer manual). **Simpan = langsung tayang** — setiap
perubahan disimpan ke database lewat `/api/content` dan langsung terlihat di
situs publik, tanpa `git push` dan tanpa langkah "Terbitkan" terpisah. Badge di
header menunjukkan status simpan (menyimpan… / tersimpan & tayang / gagal —
coba lagi).

**Tab Pendaftaran** — daftar semua pendaftar nyata (bukan simulasi), filter per
status/event, tombol **Tandai Lunas**/**Batalkan** untuk konfirmasi transfer
manual, dan **Ekspor CSV**.

> Konten situs (event, teks, warna) disimpan di **database** (tabel
> `site_content` di `yakesma-api`), dibaca situs publik lewat `GET
> /api/content` dengan fallback ke salinan lokal/bawaan bila API tak
> terjangkau. **Pendaftaran** disimpan terpisah di tabel `registrations` —
> lihat [`../yakesma-api/README.md`](../yakesma-api/README.md).

## Kustomisasi

Cara yang benar: lewat **panel admin** (tab Event/Pengaturan) — simpan langsung
tayang, tak ada file untuk diedit manual. Tapi kalau perlu tahu strukturnya:

- **Nama, tagline, kontak, warna, rekening bank** → objek `site` di tabel
  `site_content` (fallback bawaan ada di `_DEFAULT_SITE`, dekat atas
  `index.html`, dipakai kalau API tak terjangkau).
- **Daftar event** → array `events` di `site_content`. Tiap event: `slug,
  title, category, speaker, location, city, online, date, timeStart, timeEnd,
  price (0 = gratis), quota, registered, gradient, emoji, excerpt, about[],
  benefits[], featured`. `date` wajib diisi (divalidasi client & server).
- **Logo** → ganti file `yakesma-logo.png` (lockup) & `yakesma-mark.png` (ikon)
  di repo, lalu `git push` (aset gambar, bukan konten teks, jadi tetap lewat git).

## Untuk produksi

CDN Play Tailwind & Babel-in-browser cocok untuk demo/internal. Untuk produksi,
kompilasi Tailwind (Tailwind CLI/PostCSS) dan pre-compile JSX. Versi berbasis
framework juga tersedia di folder `../event-platform` (Next.js) untuk di-build
di mesin yang mendukung native tooling.
