<?php
require_once '../lib/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "followme_product");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil semua data dari tabel keluar
$sql = "SELECT * FROM keluar ORDER BY tanggal DESC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

// HTML untuk PDF
$html = '
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Data Stock Barang - Barang Keluar</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Varian</th>
                <th>Kode Barang</th>
                <th>Keterangan</th>
                <th>Jumlah Keluar</th>
            </tr>
        </thead>
        <tbody>';

// Isi data tabel
$i = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $tanggal = date('d-m-Y', strtotime($row['tanggal']));
    $html .= '<tr>
        <td>' . $i++ . '</td>
        <td>' . htmlspecialchars($tanggal) . '</td>
        <td>' . htmlspecialchars($row['kategori']) . '</td>
        <td>' . htmlspecialchars($row['varian']) . '</td>
        <td>' . htmlspecialchars($row['kodebarang']) . '</td>
        <td>' . htmlspecialchars($row['keterangan']) . '</td>
        <td>' . htmlspecialchars($row['jumlahkeluar']) . '</td>
    </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// Render PDF
$options = new Options();
$options->set("isHtml5ParserEnabled", true);
$options->set("isPhpEnabled", true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->render();

// Nama file
$filename = "stok_barang_keluar.pdf";
$dompdf->stream($filename, array("Attachment" => 0));
?>
