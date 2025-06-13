<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Perbaiki nama database (hilangkan spasi)
$conn = mysqli_connect("localhost", "root", "", "followme_product");

// Cek koneksi
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}


if(isset($_POST['deleteeksklusif'])){
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from eksklusif where idbarang='$idb'");
    if($hapus){
        header("Location: eksklusif.php");
        exit(); // Tambahkan exit setelah header
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
};


if(isset($_POST['deleteexrait'])){
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from exrait where idbarang='$idb'");
    if($hapus){
        header("Location: exrait.php");
        exit(); // Tambahkan exit setelah header
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
};


if(isset($_POST['deleteaerosols'])){
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from aerosols where idbarang='$idb'");
    if($hapus){
        header("Location: aerosols.php");
        exit(); // Tambahkan exit setelah header
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
};


if(isset($_POST['deletedisffuser'])){
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from disffuser where idbarang='$idb'");
    if($hapus){
        header("Location: disffuser.php");
        exit(); // Tambahkan exit setelah header
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
};


if(isset($_POST['deletehaircare'])){
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from haircare where idbarang='$idb'");
    if($hapus){
        header("Location: haircare.php");
        exit(); // Tambahkan exit setelah header
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
};


if (isset($_POST['keluar'])) {
    $varian = mysqli_real_escape_string($conn, $_POST['varian']);
    $kodebarang = intval($_POST['kodebarang']);
    $jumlahKeluar = intval($_POST['jumlahkeluar']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);

    if (empty($varian) || $kodebarang <= 0 || $jumlahKeluar <= 0) {
        die("Data tidak lengkap atau jumlah tidak valid.");
    }

    // Contoh daftar tabel kategori
    $tabels = ['Eksklusif', 'Exrait', 'Sanju', 'aerosols', 'disffuser', 'haircare'];
    $tabelDitemukan = null;
    $stokSekarang = 0;

    // Cari stok di tabel-tabel kategori
    foreach ($tabels as $tabel) {
        $query = "SELECT stock FROM $tabel WHERE varian='$varian' AND kodebarang=$kodebarang AND lokasi='$lokasi' LIMIT 1";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            $stokSekarang = intval($data['stock']);
            $tabelDitemukan = $tabel;
            break;
        }
    }

    if ($tabelDitemukan === null) {
        die("Varian tidak dikenali atau stok kosong di lokasi $lokasi.");
    }

    if ($stokSekarang < $jumlahKeluar) {
        die("Stok tidak cukup. Stok tersedia: $stokSekarang, diminta: $jumlahKeluar");
    }

    // Kurangi stok di tabel kategori yang ditemukan
    $stokBaru = $stokSekarang - $jumlahKeluar;
    $updateStockQuery = "UPDATE $tabelDitemukan SET stock=$stokBaru WHERE varian='$varian' AND kodebarang=$kodebarang AND lokasi='$lokasi'";
    if (!mysqli_query($conn, $updateStockQuery)) {
        die("Gagal update stok: " . mysqli_error($conn));
    }

    // Cek apakah sudah ada data keluar dengan varian dan keterangan sama
    $cekQuery = "SELECT idbarang, jumlahkeluar FROM keluar WHERE varian='$varian' AND keterangan='$keterangan' LIMIT 1";
    $resCek = mysqli_query($conn, $cekQuery);
    if (!$resCek) {
        die("Query cek keluar gagal: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($resCek) > 0) {
        $dataKeluar = mysqli_fetch_assoc($resCek);
        $idbarang = $dataKeluar['idbarang'];
        $jumlahBaru = $dataKeluar['jumlahkeluar'] + $jumlahKeluar;

        $updateKeluarQuery = "UPDATE keluar SET jumlahkeluar=$jumlahBaru WHERE idbarang=$idbarang";
        if (!mysqli_query($conn, $updateKeluarQuery)) {
            die("Update keluar gagal: " . mysqli_error($conn));
        }
    } else {
        // Insert data baru jika belum ada
        $insertQuery = "INSERT INTO keluar (tanggal, kategori, varian, kodebarang, jumlahkeluar, keterangan)
                        VALUES (NOW(), '$tabelDitemukan', '$varian', $kodebarang, $jumlahKeluar, '$keterangan')";
        if (!mysqli_query($conn, $insertQuery)) {
            die("Insert keluar gagal: " . mysqli_error($conn));
        }
    }

    echo "<script>alert('Stok berhasil dikurangi & data keluar disimpan.'); window.location.href='keluar.php';</script>";
}


if(isset($_POST['deletekeluar'])){
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from keluar where idbarang='$idb'");
    if($hapus){
        header("Location: stock_keluar.php");
        exit(); // Tambahkan exit setelah header
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
};

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';

$sql = "SELECT * FROM keluar WHERE 1";

if ($search !== '') {
    $sql .= " AND varian LIKE '%$search%'";
}

if ($kategori !== '') {
    $sql .= " AND kategori = '$kategori'";
}

$sql .= " ORDER BY tanggal DESC";

$result = mysqli_query($conn, $sql);








