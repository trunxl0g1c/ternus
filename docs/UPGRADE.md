# Memperbarui v1 ke v1.1 tanpa menghapus data

1. Hentikan penggunaan aplikasi oleh seluruh pengguna. Login owner dan unduh backup JSON lewat Pengaturan. Ekspor juga database melalui phpMyAdmin. Simpan backup di luar htdocs.
2. Simpan salinan folder aplikasi lama dan `server/config.php` di luar htdocs. Catat nama database, host, port, username dan password yang sedang dipakai.
3. Stop Apache di XAMPP. Ganti folder aplikasi lama dengan folder `ternus` dari ZIP v1.1. Jangan menimpa sebagian file; pergantian folder memastikan JS/CSS lama tidak ikut dimuat.
4. Salin kembali `server/config.php` milik instalasi Anda ke folder versi baru. Ini penting bila kredensial atau nama database berbeda dari default.
5. Start Apache dan MySQL, buka `http://localhost/ternus/index.html`, lalu lakukan hard refresh **Ctrl+F5**. Login dengan akun yang sama. Data berada di database yang sama; tidak ada impor ulang master atau reset stok.
6. Periksa saldo tiga lokasi, batch, invoice outstanding dan satu alur transaksi uji sebelum kembali beroperasi. Gunakan database latihan terpisah apabila ingin mencoba transaksi baru.

**Tidak ada migrasi schema atau reset database pada upgrade ini.** Tabel `app_state`, format backup `ternus-backup-1`, ID data, password hash dan nama command tetap kompatibel. Jika installer muncul padahal pernah terpasang, berhenti dahulu dan periksa MySQL/config; jangan membuat instalasi baru untuk menutupi masalah koneksi.

Untuk rollback kode, stop penggunaan/Apache lalu kembalikan folder lama dan config yang benar. Karena tidak ada perubahan format data pada v1.1, kode lama dapat membaca database yang sama. Jangan memulihkan backup lama jika tidak bermaksud membuang transaksi yang tercatat setelah backup. Simpan backup baru sebelum keputusan pemulihan.

Instalasi baru: ikuti README.md; akun owner dan database dibuat melalui halaman pemasangan localhost.
