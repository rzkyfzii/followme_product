<?php
require 'function.php';
include 'kodeBarangMap.php';

$tanggal = date("Y-m-d");

// ==========================
// === SCAN BARCODE MANUAL ===
// ==========================
if (!empty($_POST['barcode_scanned'])) {
    $kodebarang = trim($_POST['barcode_scanned']);
    $qty = isset($_POST['stock']) ? (int) $_POST['stock'] : 1;
    $keterangan = isset($_POST['keterangan']) && $_POST['keterangan'] !== '' ? trim($_POST['keterangan']) : '-';
    if ($qty < 1) $qty = 1;

    // ... kode validasi kodebarang & varian sama seperti kamu punya ...

    if (!isset($kodeBarangMap[$kodebarang])) {
        die("❌ Barcode $kodebarang tidak ditemukan dalam map.");
    }

    $varian = $kodeBarangMap[$kodebarang]['varian'];
    $kategori = $kodeBarangMap[$kodebarang]['kategori'];

    $varianExrait = ['OMBRE', 'ROSES', 'MAGIC OF NATURE', 'LOVE OUD', 'MATCHER', 'JASMINUM SAMBAC'];
    $varianEksklusif = ['GLORIOUS MOONLIGHT', 'L AME DE L OCEAN', 'SENSO DI BLOSSOM', 'SUNSET FALVOR'];
     $Aerosols = [
  'MEN BODY SPRAY (RED)',
  'MEN BODY SPRAY (WHITE)',
  'MEN BODY SPRAY (YELLOW)',
  'SENCE BODY SPRAY HAPPY',
  'SENCE BODY SPRAY LOVELY',
  'SENCE BODY SPRAY ROMANCE',
  'SENCE BODY SPRAY JOYFUL'
];
    

    if (in_array(strtoupper($varian), array_map('strtoupper', $varianExrait))) {
        $tabelTujuan = 'exrait';
    } elseif (in_array(strtoupper($varian), array_map('strtoupper', $varianEksklusif))) {
        $tabelTujuan = 'eksklusif';
        } elseif ($kategori === 'BODY SPRAY') {
            $tabelTujuan = 'aerosols';
    } else {
        die("❌ Varian '$varian' tidak cocok dengan kategori tabel manapun.");
    }

    $cek = mysqli_query($conn, "SELECT * FROM $tabelTujuan WHERE kodebarang = '$kodebarang' AND varian = '$varian'");
    if (mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_assoc($cek);
        $idb = $data['idbarang'];
        $stockLama = (int)$data['stock'];

        if ($stockLama < $qty) {
            die("❌ Stok tidak cukup. Tersedia: $stockLama, Diminta: $qty");
        }

        $stockBaru = $stockLama - $qty;
        $update = mysqli_query($conn, "UPDATE $tabelTujuan 
            SET stock = '$stockBaru', tanggal = '$tanggal' 
            WHERE idbarang = '$idb'");

        if ($update) {
            mysqli_query($conn, "INSERT INTO keluar (tanggal, varian, kategori, kodebarang, keterangan, jumlahkeluar)
                VALUES ('$tanggal', '$varian', '$kategori', '$kodebarang', '$keterangan', '$qty')");
        }
    } else {
        die("❌ Barang tidak ditemukan di database.");
    }

    header("Location: stock_keluar.php?status=sukses");
    exit;
}

// ========================
// === UPLOAD CSV FILE ===
// ========================
if (isset($_FILES['barcode_file']) && $_FILES['barcode_file']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['barcode_file']['tmp_name'];
    $lines = file($fileTmp, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $berhasil = 0;
    $gagal = [];

    $varianExrait = ['OMBRE', 'ROSES', 'MAGIC OF NATURE', 'LOVE OUD', 'MATCHER', 'JASMINUM SAMBAC'];
    $varianEksklusif = ['GLORIOUS MOONLIGHT', 'L AME DE L OCEAN', 'SENSO DI BLOSSOM', 'SUNSET FALVOR'];
    $Aerosols = [
  'MEN BODY SPRAY (RED)',
  'MEN BODY SPRAY (WHITE)',
  'MEN BODY SPRAY (YELLOW)',
  'SENCE BODY SPRAY HAPPY',
  'SENCE BODY SPRAY LOVELY',
  'SENCE BODY SPRAY ROMANCE',
  'SENCE BODY SPRAY JOYFUL'
];
    foreach ($lines as $line) {
        $parts = explode(",", trim($line));
        $kodebarang = trim($parts[0]);
        $qty = isset($parts[1]) ? (int)$parts[1] : 1;
        $keteranganFromForm = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : 'Upload CSV';
$keterangan = $keteranganFromForm;


        if ($qty < 1) $qty = 1;

        if (!isset($kodeBarangMap[$kodebarang])) {
            $gagal[] = "$kodebarang (tidak ditemukan di map)";
            continue;
        }

        $varian = $kodeBarangMap[$kodebarang]['varian'];
        $kategori = $kodeBarangMap[$kodebarang]['kategori'];

        if (in_array(strtoupper($varian), array_map('strtoupper', $varianExrait))) {
            $tabelTujuan = 'exrait';
        } elseif (in_array(strtoupper($varian), array_map('strtoupper', $varianEksklusif))) {
            $tabelTujuan = 'eksklusif';
        } elseif ($kategori === 'BODY SPRAY') {
            $tabelTujuan = 'aerosols';
        } else {
            $gagal[] = "$kodebarang (varian '$varian' tidak masuk kategori manapun)";
            continue;
        }

        $cek = mysqli_query($conn, "SELECT * FROM $tabelTujuan WHERE kodebarang = '$kodebarang'");
        if (mysqli_num_rows($cek) > 0) {
            $data = mysqli_fetch_assoc($cek);
            $idb = $data['idbarang'];
            $stockLama = (int)$data['stock'];

            if ($stockLama < $qty) {
                $gagal[] = "$kodebarang (stok tidak cukup: $stockLama)";
                continue;
            }

            $stockBaru = $stockLama - $qty;
            $update = mysqli_query($conn, "UPDATE $tabelTujuan 
                SET stock = '$stockBaru', tanggal = '$tanggal' 
                WHERE idbarang = '$idb'");

            if ($update) {
                mysqli_query($conn, "INSERT INTO keluar (tanggal, varian, kategori, kodebarang, keterangan, jumlahkeluar)
                    VALUES ('$tanggal', '$varian', '$kategori', '$kodebarang', '$keterangan', '$qty')");
                $berhasil++;
            } else {
                $gagal[] = "$kodebarang (gagal update stok)";
            }
        } else {
            $gagal[] = "$kodebarang (barang tidak ditemukan)";
        }
    }

    if (empty($gagal)) {
        header("Location: stock_keluar.php?status=upload_sukses");
    } else {
        echo "Beberapa item gagal diproses:<br>" . implode("<br>", $gagal);
    }

    exit;
}

echo "Tidak ada input yang diterima.";
exit;
?>
