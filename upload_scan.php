<?php
require 'function.php';
include 'kodeBarangMap.php';

// Jika data berasal dari kamera scan (input hidden)
if (!empty($_POST['barcode_scanned'])) {
    $kodebarang = trim($_POST['barcode_scanned']);
    $qty = 1;
    $tanggal = date("Y-m-d");

    if (!isset($kodeBarangMap[$kodebarang])) {
      echo "<script>alert('❌ Barcode $kodebarang tidak ditemukan dalam map.'); window.location.href='index.php';</script>";
exit;
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
        echo "<script>alert('❌ Varian \"$varian\" tidak termasuk dalam kategori tabel manapun.'); window.location.href='index.php';</script>";
exit;
    }

    $cek = mysqli_query($conn, "SELECT * FROM $tabelTujuan WHERE varian = '$varian' AND lokasi = '$lokasi'");
    if (mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_assoc($cek);
        $idb = $data['idbarang'];
        $new_stock = $data['stock'] + $qty;
        mysqli_query($conn, "UPDATE $tabelTujuan SET stock = '$new_stock', tanggal = '$tanggal' WHERE idbarang = '$idb'");
    } else {
        mysqli_query($conn, "INSERT INTO $tabelTujuan (tanggal, kategori, varian, kodebarang, stock, lokasi)
            VALUES ('$tanggal', '$kategori', '$varian', '$kodebarang', '$qty', '$lokasi')");
    }

    header("Location: index.php");
    exit;
}

// Jika data berasal dari file .txt
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

        if (!isset($kodeBarangMap[$kodebarang])) {
            $gagal[] = "$kodebarang (tidak ditemukan di map)";
            continue;
        }

        $varian = $kodeBarangMap[$kodebarang]['varian'];
        $kategori = $kodeBarangMap[$kodebarang]['kategori'];
        $tanggal = date("Y-m-d");

        $tabelTujuan = '';
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

        $cek = mysqli_query($conn, "SELECT * FROM $tabelTujuan WHERE varian = '$varian' AND lokasi = '$lokasi'");
        if (mysqli_num_rows($cek) > 0) {
            $data = mysqli_fetch_assoc($cek);
            $idb = $data['idbarang'];
            $new_stock = $data['stock'] + $qty;
            $update = mysqli_query($conn, "UPDATE $tabelTujuan SET stock = '$new_stock', tanggal = '$tanggal' WHERE idbarang = '$idb'");
            if ($update) $berhasil++;
        } else {
            $insert = mysqli_query($conn, "INSERT INTO $tabelTujuan (tanggal, kategori, varian, kodebarang, stock, lokasi)
                VALUES ('$tanggal', '$kategori', '$varian', '$kodebarang', '$qty', '$lokasi')");
            if ($insert) $berhasil++;
        }
    }

    header("Location: index.php");
    exit;
}
echo "<script>alert('Upload selesai: $berhasil berhasil, " . count($gagal) . " gagal.'); window.location.href='index.php';</script>";
exit;


// Jika tidak ada input
echo "Tidak ada input yang diterima.";
exit;
?>
