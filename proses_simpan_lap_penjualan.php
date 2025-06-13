<?php
require 'function.php'; // koneksi DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $no_resi = mysqli_real_escape_string($conn, $_POST['no_resi']);
    $no_pesanan = mysqli_real_escape_string($conn, $_POST['no_pesanan']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $varian = mysqli_real_escape_string($conn, $_POST['varian']);
    $free_vial = mysqli_real_escape_string($conn, $_POST['free_vial']);
    $platform = mysqli_real_escape_string($conn, $_POST['platform']);
    $cogs_product = floatval($_POST['cogs_product']);
    $packing_charge = floatval($_POST['packing_charge']);
    $cogs_free_vial = floatval($_POST['cogs_free_vial']);
    $product_terjual = intval($_POST['product_terjual']);
    $net_income = floatval($_POST['net_income']);

    // Handle upload image
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExts)) {
            // Rename file agar unik
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = 'uploads/';
            $destPath = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imageName = $newFileName;
            } else {
                die("Error upload file gambar.");
            }
        } else {
            die("Format file gambar tidak diperbolehkan. Hanya jpg, jpeg, png, gif.");
        }
    }

    // Insert ke DB
    $sql = "INSERT INTO lap_penjualan 
    (tanggal, no_resi, no_pesanan, kategori, varian, free_vial, platform, cogs_product, packing_charge, cogs_free_vial, product_terjual, net_income, image)
    VALUES 
    ('$tanggal', '$no_resi', '$no_pesanan', '$kategori', '$varian', '$free_vial', '$platform', $cogs_product, $packing_charge, $cogs_free_vial, $product_terjual, $net_income, " . ($imageName ? "'$imageName'" : "NULL") . ")";

    if (mysqli_query($conn, $sql)) {
        header("Location: laporan_penjualan.php?status=success");
        exit;
    } else {
        die("Error simpan data: " . mysqli_error($conn));
    }
} else {
    die("Metode request tidak valid.");
}
  