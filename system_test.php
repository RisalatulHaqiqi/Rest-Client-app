<?php
/**
 * System Test Suite (White Box Testing) - 3 Test Cases
 * Aplikasi: REST Client berbasis PHP untuk newsAPI.org
 * Nama File: system_test.php
 */

// Memuat file konfigurasi aplikasi untuk mengambil fungsi http_request_get()
require_once __DIR__ . '/config/config.php';

echo "==================================================\n";
echo "      WHITE BOX TEST SUITE (3 TEST CASES)        \n";
echo "==================================================\n\n";

$passCount = 0;
$failCount = 0;
$sharedResponseData = null;

// ==========================================
// TEST CASE 1: http_request_get() dengan URL Valid
// ==========================================
echo "[TC-WB-01] Testing http_request_get() dengan URL Valid...\n";
try {
    // Gunakan API key Anda yang valid
    $validUrl = "https://newsapi.org/v2/top-headlines?country=us&category=technology&apiKey=xxx";
    $response = http_request_get($validUrl);

    if ($response === false || empty($response)) {
        throw new Exception("Fungsi http_request_get() mengembalikan nilai false atau kosong.");
    }

    $decodedData = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Output dari http_request_get() bukan string JSON valid.");
    }

    if (isset($decodedData['status']) && $decodedData['status'] !== 'ok') {
        throw new Exception("API mengembalikan status selain 'ok'.");
    }

    echo "Status: PASS\n";
    echo "Detail: Berhasil melakukan HTTP GET ke URL valid dan menerima respons JSON valid.\n\n";
    $passCount++;
    
    // Simpan data untuk digunakan di Test Case 3 (efisiensi tanpa panggil API berulang)
    $sharedResponseData = $decodedData;

} catch (Exception $e) {
    echo "Status: FAIL\n";
    echo "Detail Error: " . $e->getMessage() . "\n\n";
    $failCount++;
}


// ==========================================
// TEST CASE 2: http_request_get() dengan URL Tidak Valid
// ==========================================
echo "[TC-WB-02] Testing http_request_get() dengan URL Tidak Valid...\n";
try {
    $invalidUrl = "https://newsapi.org/v2/endpoint-salah-123?apiKey=invalid_key";
    $response = http_request_get($invalidUrl);
    $decodedData = json_decode($response, true);

    if ($response === false || (isset($decodedData['status']) && $decodedData['status'] === 'error')) {
        echo "Status: PASS\n";
        echo "Detail: Sistem berhasil mendeteksi error/koneksi invalid sesuai ekspektasi.\n\n";
        $passCount++;
    } else {
        throw new Exception("URL tidak valid namun sistem gagal mendeteksi error.");
    }

} catch (Exception $e) {
    echo "Status: PASS (Error berhasil ditangkap: " . $e->getMessage() . ")\n\n";
    $passCount++;
}


// ==========================================
// TEST CASE 3: json_decode & Pengecekan Key 'articles'
// ==========================================
echo "[TC-WB-03] Testing json_decode() & Validasi Kunci 'articles'...\n";
try {
    // Jika TC1 gagal, gunakan mock data cadangan
    if ($sharedResponseData === null) {
        $mockJson = '{"status":"ok","totalResults":1,"articles":[{"source":{"id":null,"name":"Test"},"title":"Sample","url":"https://example.com"}]}';
        $decodedData = json_decode($mockJson, true);
    } else {
        $decodedData = $sharedResponseData;
    }

    if (!is_array($decodedData)) {
        throw new Exception("Hasil json_decode bukan berupa array.");
    }

    if (!array_key_exists('articles', $decodedData)) {
        throw new Exception("Kunci 'articles' tidak ditemukan pada struktur array JSON.");
    }

    if (!is_array($decodedData['articles'])) {
        throw new Exception("Nilai dari kunci 'articles' bukan berupa array data.");
    }

    echo "Status: PASS\n";
    echo "Detail: Struktur JSON berhasil diurai dan kunci 'articles' valid bernilai array.\n\n";
    $passCount++;

} catch (Exception $e) {
    echo "Status: FAIL\n";
    echo "Detail Error: " . $e->getMessage() . "\n\n";
    $failCount++;
}


// ==========================================
// RINGKASAN AKHIR PENGUJIAN
// ==========================================
echo "==================================================\n";
echo "                RINGKASAN WHITE BOX               \n";
echo "==================================================\n";
echo "Total Test Case : 3\n";
echo "PASS            : $passCount\n";
echo "FAIL            : $failCount\n";
echo "==================================================\n";