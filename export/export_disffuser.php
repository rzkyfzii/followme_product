<?php
require_once '../lib/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil parameter lokasi dari URL
$lokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : [];

// Pastikan selalu array
if (!is_array($lokasi)) {
    $lokasi = [$lokasi];
}

// Lokasi yang diizinkan
$allowedLokasi = ['Gudang A', 'Gudang C'];

// Hilangkan elemen kosong di array lokasi
$lokasi = array_filter($lokasi, function($v) {
    return !empty($v);
});

// Jika ada input lokasi, validasi isinya
if (!empty($lokasi)) {
    foreach ($lokasi as $l) {
        if (!in_array($l, $allowedLokasi)) {
            die("Parameter lokasi tidak valid.");
        }
    }
}

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "followme_product");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Buat klausa WHERE berdasarkan lokasi (jika ada)
$whereSql = "";
if (!empty($lokasi)) {
    $escapedLokasi = array_map(function($v) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $v) . "'";
    }, $lokasi);
    $whereSql = "WHERE lokasi IN (" . implode(',', $escapedLokasi) . ")";
}

// Ambil data dari tabel eksklusif
$sql = "SELECT * FROM disffuser $whereSql ORDER BY tanggal DESC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

// Judul lokasi
$judulLokasi = !empty($lokasi) ? implode(", ", $lokasi) : "Semua Lokasi";

// Mulai HTML untuk PDF
$html = '<html><head>
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    h2, h4 { text-align: center; margin: 0 0 10px 0; }
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: center; }
    th { background-color: #f2f2f2; }
</style>
</head><body>';

$html .= '<h2>Data Stock Barang - Varian: Disffuser</h2>';
$html .= '<h4>Lokasi: ' . htmlspecialchars($judulLokasi) . '</h4>';
$html .= '<table>';
$html .= '<thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Varian</th>
                <th>Kode Barang</th>
                <th>Stock</th>
                <th>Lokasi</th>
            </tr>
          </thead><tbody>';

$i = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $tanggal = date('d-m-Y', strtotime($row['tanggal']));
    $html .= '<tr>';
    $html .= '<td>' . $i++ . '</td>';
    $html .= '<td>' . htmlspecialchars($tanggal) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['varian']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['kodebarang']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['stock']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['lokasi']) . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';
$html .= '</body></html>';

// Render PDF
$options = new Options();
$options->set("isHtml5ParserEnabled", true);
$options->set("isPhpEnabled", true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->render();

$filename = "stok_disffuser_" . str_replace(", ", "_", strtolower($judulLokasi)) . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0));
