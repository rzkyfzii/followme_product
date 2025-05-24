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

// tambah barang eksklusif
if (isset($_POST['eksklusif'])) {
    $inputTanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d H:i:s');
    $tanggal = date('Y-m-d H:i:s', strtotime($inputTanggal));

    $varian = mysqli_real_escape_string($conn, $_POST['varian'] ?? '');
    $kodebarang = mysqli_real_escape_string($conn, $_POST['kodebarang'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi'] ?? '');
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori'] ?? '');

    if (empty($varian) || empty($kodebarang) || $stock <= 0 || empty($lokasi)) {
        echo "Semua field harus diisi dengan benar.";
        exit;
    }

    $check = mysqli_query($conn, "SELECT * FROM eksklusif 
        WHERE varian = '$varian' AND kodebarang = '$kodebarang' AND lokasi = '$lokasi' 
        LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        $existing = mysqli_fetch_assoc($check);
        $idbarang = $existing['idbarang'];

        $update = mysqli_query($conn, "UPDATE eksklusif SET 
            stock = stock + $stock, tanggal = '$tanggal' 
            WHERE idbarang = '$idbarang'");

        if ($update) {
            header("Location: eksklusif.php");
            exit();
        } else {
            echo "Gagal update stock: " . mysqli_error($conn);
        }
    } else {
        $idbarang = uniqid("BRG-");

        $insert = mysqli_query($conn, "INSERT INTO eksklusif 
            (idbarang, kategori, varian, kodebarang, stock, lokasi, tanggal) VALUES 
            ('$idbarang', '$kategori', '$varian', '$kodebarang', $stock, '$lokasi', '$tanggal')");

        if ($insert) {
            header("Location: eksklusif.php");
            exit();
        } else {
            echo "Gagal tambah barang: " . mysqli_error($conn);
        }
    }
}

if(isset($_POST['deleteEksklusif'])){
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from eksklusif where idbarang='$idb'");
    if($hapus){
        header("Location: eksklusif.php");
        exit(); // Tambahkan exit setelah header
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
};

if (isset($_POST['exrait'])) {
    // Ambil dan proses tanggal
    $inputTanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d H:i:s');
    $tanggal = date('Y-m-d H:i:s', strtotime($inputTanggal));

    // Ambil dan sanitasi input dari form
    $varian = mysqli_real_escape_string($conn, $_POST['varian'] ?? '');
    $kodebarang = mysqli_real_escape_string($conn, $_POST['kodebarang'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi'] ?? '');
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori'] ?? '');

    // Validasi input
    if (empty($varian) || empty($kodebarang) || $stock <= 0 || empty($lokasi)) {
        echo "Semua field harus diisi dengan benar.";
        exit;
    }

    // Cek apakah kombinasi varian + kodebarang + lokasi sudah ada
    $check = mysqli_query($conn, "SELECT * FROM exrait
        WHERE varian = '$varian' AND kodebarang = '$kodebarang' AND lokasi = '$lokasi' 
        LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        // Barang dengan kombinasi tersebut sudah ada → update stok
        $existing = mysqli_fetch_assoc($check);
        $idbarang = $existing['idbarang'];

        $update = mysqli_query($conn, "UPDATE exrait SET 
            stock = stock + $stock,
            tanggal = '$tanggal'
            WHERE idbarang = '$idbarang'");

        if ($update) {
            header("Location: exrait.php");
            exit();
        } else {
            echo "Gagal update stock: " . mysqli_error($conn);
        }

    } else {
        // Kombinasi belum ada → insert data baru
        $idbarang = uniqid("BRG-");

        // Jika lokasi Gudang C, masukkan juga keterangan
        if ($lokasi === 'Gudang C') {
            $insert = mysqli_query($conn, "INSERT INTO exrait 
                (idbarang, kategori, varian, kodebarang, stock, lokasi, tanggal) VALUES 
                ('$idbarang', '$kategori', '$varian', '$kodebarang', $stock, '$lokasi', '$tanggal')");
        } else {
            $insert = mysqli_query($conn, "INSERT INTO exrait 
                (idbarang, kategori, varian, kodebarang, stock, lokasi, tanggal) VALUES 
                ('$idbarang', '$kategori', '$varian', '$kodebarang', $stock, '$lokasi', '$tanggal')");
        }

        if ($insert) {
            header("Location: exrait.php");
            exit();
        } else {
            echo "Gagal tambah barang: " . mysqli_error($conn);
        }
    }
}

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

if (isset($_POST['sanju'])) {
    // Ambil dan proses tanggal
    $inputTanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d H:i:s');
    $tanggal = date('Y-m-d H:i:s', strtotime($inputTanggal));

    // Ambil dan sanitasi input dari form
    $varian = mysqli_real_escape_string($conn, $_POST['varian'] ?? '');
    $kodebarang = mysqli_real_escape_string($conn, $_POST['kodebarang'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi'] ?? '');
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori'] ?? '');

    // Validasi input
    if (empty($varian) || empty($kodebarang) || $stock <= 0 || empty($lokasi)) {
        echo "Semua field harus diisi dengan benar.";
        exit;
    }

    // Cek apakah kombinasi varian + kodebarang + lokasi sudah ada
    $check = mysqli_query($conn, "SELECT * FROM sanju
        WHERE varian = '$varian' AND kodebarang = '$kodebarang' AND lokasi = '$lokasi' 
        LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        // Barang dengan kombinasi tersebut sudah ada → update stok
        $existing = mysqli_fetch_assoc($check);
        $idbarang = $existing['idbarang'];

        $update = mysqli_query($conn, "UPDATE sanju SET 
            stock = stock + $stock,
            tanggal = '$tanggal'
            WHERE idbarang = '$idbarang'");

        if ($update) {
            header("Location: sanju.php");
            exit();
        } else {
            echo "Gagal update stock: " . mysqli_error($conn);
        }

    } else {
        // Kombinasi belum ada → insert data baru
        $idbarang = uniqid("BRG-");

        // Jika lokasi Gudang C, masukkan juga keterangan
        if ($lokasi === 'Gudang C') {
            $insert = mysqli_query($conn, "INSERT INTO sanju 
                (idbarang, kategori, varian, kodebarang, stock, lokasi, tanggal) VALUES 
                ('$idbarang', '$kategori', '$varian', '$kodebarang', $stock, '$lokasi', '$tanggal')");
        } else {
            $insert = mysqli_query($conn, "INSERT INTO sanju 
                (idbarang, kategori, varian, kodebarang, stock, lokasi, tanggal) VALUES 
                ('$idbarang', '$kategori', '$varian', '$kodebarang', $stock, '$lokasi', '$tanggal')");
        }

        if ($insert) {
            header("Location: sanju.php");
            exit();
        } else {
            echo "Gagal tambah barang: " . mysqli_error($conn);
        }
    }
}

if(isset($_POST['deletesanju'])){
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from sanju where idbarang='$idb'");
    if($hapus){
        header("Location: sanju.php");
        exit(); // Tambahkan exit setelah header
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
};

if (isset($_POST['aerosols'])) {
    // Ambil dan proses tanggal, default sekarang jika kosong
    $inputTanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d H:i:s');
    $tanggal = date('Y-m-d H:i:s', strtotime($inputTanggal));

    // Sanitasi input dari form
    $varian = mysqli_real_escape_string($conn, $_POST['varian'] ?? '');
    $kodebarang = mysqli_real_escape_string($conn, $_POST['kodebarang'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi'] ?? '');
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori'] ?? '');


    // Validasi input
    if (empty($varian) || empty($kodebarang) || $stock <= 0 || empty($lokasi)) {
        echo "Semua field harus diisi dengan benar.";
        exit;
    }

    // Cek apakah kombinasi varian + kodebarang + lokasi sudah ada
    $check = mysqli_query($conn, "SELECT * FROM aerosols WHERE varian = '$varian' AND kodebarang = '$kodebarang' AND lokasi = '$lokasi' AND kategori = '$kategori' LIMIT 1");
    if (!$check) {
        echo "Error cek data: " . mysqli_error($conn);
        exit;
    }

    if (mysqli_num_rows($check) > 0) {
        // Update stok yang sudah ada
        $existing = mysqli_fetch_assoc($check);
        $idbarang = $existing['idbarang'];

        $update = mysqli_query($conn, "UPDATE aerosols SET stock = stock + $stock, tanggal = '$tanggal' WHERE idbarang = '$idbarang'");
        if ($update) {
            header("Location: aerosols.php");
            exit();
        } else {
            echo "Gagal update stock: " . mysqli_error($conn);
            exit;
        }
    } else {
        // Insert data baru
        $idbarang = uniqid("BRG-");

        $insert = mysqli_query($conn, "INSERT INTO aerosols (idbarang, kategori, varian, kodebarang, stock, lokasi, tanggal) VALUES ('$idbarang', '$kategori', '$varian', '$kodebarang', $stock, '$lokasi', '$tanggal')");
        if ($insert) {
            header("Location: aerosols.php");
            exit();
        } else {
            echo "Gagal tambah barang: " . mysqli_error($conn);
            exit;
        }
    }
}

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

if (isset($_POST['disffuser'])) {
    // Ambil dan proses tanggal, default sekarang jika kosong
    $inputTanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d H:i:s');
    $tanggal = date('Y-m-d H:i:s', strtotime($inputTanggal));

    // Sanitasi input dari form
    $varian = mysqli_real_escape_string($conn, $_POST['varian'] ?? '');
    $kodebarang = mysqli_real_escape_string($conn, $_POST['kodebarang'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi'] ?? '');
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori'] ?? '');

    // Validasi input
    if (empty($varian) || empty($kodebarang) || $stock <= 0 || empty($lokasi)) {
        echo "Semua field harus diisi dengan benar.";
        exit;
    }

    // Cek apakah kombinasi varian + kodebarang + lokasi sudah ada
    $check = mysqli_query($conn, "SELECT * FROM disffuser WHERE varian = '$varian' AND kodebarang = '$kodebarang' AND lokasi = '$lokasi' AND kategori = '$kategori' LIMIT 1");
    if (!$check) {
        echo "Error cek data: " . mysqli_error($conn);
        exit;
    }

    if (mysqli_num_rows($check) > 0) {
        // Update stok yang sudah ada
        $existing = mysqli_fetch_assoc($check);
        $idbarang = $existing['idbarang'];

        $update = mysqli_query($conn, "UPDATE disffuser SET stock = stock + $stock, tanggal = '$tanggal' WHERE idbarang = '$idbarang'");
        if ($update) {
            header("Location: disffuser.php");
            exit();
        } else {
            echo "Gagal update stock: " . mysqli_error($conn);
            exit;
        }
    } else {
        // Insert data baru
        $idbarang = uniqid("BRG-");

        $insert = mysqli_query($conn, "INSERT INTO disffuser (idbarang, kategori, varian, kodebarang, stock, lokasi, tanggal) VALUES ('$idbarang', '$kategori', '$varian', '$kodebarang', $stock, '$lokasi', '$tanggal')");
        if ($insert) {
            header("Location: disffuser.php");
            exit();
        } else {
            echo "Gagal tambah barang: " . mysqli_error($conn);
            exit;
        }
    }
}

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

if (isset($_POST['haircare'])) {
    // Ambil dan proses tanggal, default sekarang jika kosong
    $inputTanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d H:i:s');
    $tanggal = date('Y-m-d H:i:s', strtotime($inputTanggal));

    // Sanitasi input dari form
    $varian = mysqli_real_escape_string($conn, $_POST['varian'] ?? '');
    $kodebarang = mysqli_real_escape_string($conn, $_POST['kodebarang'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi'] ?? '');
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori'] ?? '');

    // Validasi input
    if (empty($varian) || empty($kodebarang) || $stock <= 0 || empty($lokasi)) {
        echo "Semua field harus diisi dengan benar.";
        exit;
    }

    // Cek apakah kombinasi varian + kodebarang + lokasi sudah ada
    $check = mysqli_query($conn, "SELECT * FROM haircare WHERE varian = '$varian' AND kodebarang = '$kodebarang' AND lokasi = '$lokasi' AND kategori = '$kategori' LIMIT 1");
    if (!$check) {
        echo "Error cek data: " . mysqli_error($conn);
        exit;
    }

    if (mysqli_num_rows($check) > 0) {
        // Update stok yang sudah ada
        $existing = mysqli_fetch_assoc($check);
        $idbarang = $existing['idbarang'];

        $update = mysqli_query($conn, "UPDATE haircare SET stock = stock + $stock, tanggal = '$tanggal' WHERE idbarang = '$idbarang'");
        if ($update) {
            header("Location: haircare.php");
            exit();
        } else {
            echo "Gagal update stock: " . mysqli_error($conn);
            exit;
        }
    } else {
        // Insert data baru
        $idbarang = uniqid("BRG-");

        $insert = mysqli_query($conn, "INSERT INTO haircare (idbarang, kategori, varian, kodebarang, stock, lokasi, tanggal) VALUES ('$idbarang', '$kategori', '$varian', '$kodebarang', $stock, '$lokasi', '$tanggal')");
        if ($insert) {
            header("Location: haircare.php");
            exit();
        } else {
            echo "Gagal tambah barang: " . mysqli_error($conn);
            exit;
        }
    }
}

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
    $tabels = ['Eksklusif', 'Exrait', 'Sanju'];
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
        header("Location: keluar.php");
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








