<?php
require('function.php');

// Tambah varian baru
if (isset($_POST['tambah']) && !empty($_POST['varian'])) {
    $varian = trim($_POST['varian']);
    mysqli_query($conn, "INSERT IGNORE INTO varian_diizinkan (varian) VALUES ('$varian')");
    header("Location: kelola_varian.php");
    exit;
}

// Hapus varian
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM varian_diizinkan WHERE id = $id");
    header("Location: kelola_varian.php");
    exit;
}

// Ambil data
$daftar = mysqli_query($conn, "SELECT * FROM varian_diizinkan ORDER BY varian ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Varian Diizinkan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h2 class="mb-4">Kelola Varian Diizinkan</h2>

    <form method="POST" class="row g-3 mb-4">
      <div class="col-auto">
        <input type="text" name="varian" class="form-control" placeholder="Nama varian" required>
      </div>
      <div class="col-auto">
        <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
      </div>
    </form>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>No</th>
          <th>Varian</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; while ($v = mysqli_fetch_assoc($daftar)) { ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($v['varian']); ?></td>
          <td>
            <a href="?hapus=<?= $v['id']; ?>" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-sm btn-danger">Hapus</a>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
