<?php
// get-barcode.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "followme_product"; // ganti dengan nama database kamu

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$kode = $_GET['kode'] ?? '';

$sql = "SELECT kategori, varian, kodebarang FROM eksklusif WHERE barcode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $kode);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(null);
}

$stmt->close();
$conn->close();
?>
