# Hasil pengujian v1.1 — modular + Tailwind

Pengujian 1 Oktober 2026 memakai PHP 8.3.6, MariaDB 10.11.14, Node.js 24, Tailwind CSS 4.1.18, dan Chromium headless 153 pada Linux. Paket ditujukan untuk XAMPP PHP 8.1+; belum dijalankan langsung pada Windows/XAMPP pengguna.

## Lulus

- Pemeriksaan sintaks PHP; format source memakai Prettier dan plugin PHP.
- 31 pemeriksaan aturan bisnis dari versi sebelumnya: penerimaan, WIP, campuran batch, susut/HPP, alokasi biaya, transfer parsial, reservasi, stok tidak cukup, invoice, pengiriman, pembayaran, overpayment, kredit/refund, retur karantina, opname, aset, izin sales, versi master dan saldo nonnegatif.
- 58 ES modules: import lokal tersedia, sintaks valid, tidak ada circular dependency. Perintah pemeriksaan disertakan sebagai `npm run check:modules`.
- Build Tailwind menghasilkan CSS statis lokal dari sumber komponen dan template JS/HTML.
- Integrasi HTTP/database nyata: pemasangan pada database uji baru, login/session, persistensi penerimaan, CSRF salah/tanpa token sesi, idempotensi, rollback seluruh transaksi ketika satu baris invalid, pembatasan sales serta penyembunyian biaya.
- Membaca database uji dari implementasi v1 menggunakan backend modular tanpa migrasi/reset. Kredensial dan config paket dikembalikan ke default setelah pengujian; database uji tidak ikut dikemas.
- Chromium dengan ES modules asli: login owner, navigasi 21 halaman, penerimaan tersimpan, 10 dialog fitur (produksi, transfer, opname, quotation, order, aset, barang, pelanggan, vendor, pengguna).
- Halaman dengan fixture transaksi: penelusuran batch, detail produksi/transfer/order, quotation, invoice, riwayat invoice, riwayat aset.
- Pemeriksaan visual dashboard desktop, formulir penerimaan, invoice, dan dashboard mobile lebar 390px. Dashboard mobile tidak meluap secara horizontal. Tabel lebar tetap dapat digulir di dalam panel.
- Cetak invoice dari Chromium menghasilkan PDF A4 satu halaman dengan teks/rincian transaksi terbaca pada fixture uji.

## Batas pemeriksaan

- Belum diuji langsung di Windows/XAMPP, Safari/Firefox, perangkat mobile fisik, atau printer fisik.
- Apache/.htaccess, beban penggunaan serentak berskala produksi, keamanan deployment internet, dan seluruh kombinasi input pengguna belum diuji menyeluruh.
- Pemeriksaan browser menggunakan data contoh pengujian, bukan data operasional pengguna. Ini bukan bukti seluruh fitur spesifikasi bisnis lanjutan sudah tersedia; batas fitur v1 masih tercantum di README.md.

## Mengulang pemeriksaan yang disertakan

```bash
npm ci
npm run check:modules
npm run format:check
npm run build:css
php tests/domain_test.php
```

Tes domain memakai state dalam memori dan tidak menyentuh database operasional. Gunakan `C:\xampp\php\php.exe` jika PHP belum tersedia di PATH.
