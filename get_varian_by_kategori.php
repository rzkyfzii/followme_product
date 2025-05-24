<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "followme_product");
$kategori = $_GET['kategori'] ?? '';
$data = [];

if ($kategori === 'Parfum') {
    $tables = ['sanju', 'exrait', 'eksklusif'];
    foreach ($tables as $table) {
        $res = $conn->query("SELECT varian, kodebarang FROM $table");
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
    }
} elseif ($kategori === 'Body Spray') {
    $res = $conn->query("SELECT varian, kodebarang FROM aerosols");
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
} elseif ($kategori === 'Home Care') {
    $res = $conn->query("SELECT varian, kodebarang FROM disffuser");
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
