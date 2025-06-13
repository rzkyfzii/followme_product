<?php require('function.php'); ?>
<?php require('cek.php'); ?>
<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<h2>BARANG KELUAR</h2>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <div class="card mb-4">
              <div class="card-header d-flex align-items-center">
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalScanKeluar">
Barang Keluar (Scan / Upload)
</button>
              <form method="GET" action="export/export_keluar.php" target="_blank" class="mb-0">
                <input type="hidden" name="lokasi[]" value="<?= isset($_GET['lokasi']) ? $_GET['lokasi'] : '' ?>">
                <button type="submit" class="btn btn-danger">
                  <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </button>
              </form>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-3">
                    <div class="row">
                      <div class="col-md-5">
                        <input type="text" class="form-control" name="search" placeholder="Cari Barang"
                              value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
                      </div>
                      <div class="col-md-4">
                        <select name="kategori" class="form-control">
                          <option value="">Semua Product</option>
                                <option value="EKSKLUSIF" <?= (isset($_GET['kategori']) && $_GET['kategori'] === 'EKSKLUSIF') ? 'selected' : ''; ?>>Eksklusif</option>
                                <option value="CLASSIC" <?= (isset($_GET['kategori']) && $_GET['kategori'] === 'CLASSIC') ? 'selected' : ''; ?>>Classic</option>
                                <option value="SANJU" <?= (isset($_GET['kategori']) && $_GET['kategori'] === 'SANJU') ? 'selected' : ''; ?>>Sanju</option>
                                <option value="AEROSOLS" <?= (isset($_GET['kategori']) && $_GET['kategori'] === 'AEROSOLS') ? 'selected' : ''; ?>>Aerosols</option>
                                <option value="DISFFUSER" <?= (isset($_GET['kategori']) && $_GET['kategori'] === 'DISFFUSER') ? 'selected' : ''; ?>>Disffuser</option>
                                <option value="HAIRCARE" <?= (isset($_GET['kategori']) && $_GET['kategori'] === 'HAIRCARE') ? 'selected' : ''; ?>>Hair Care</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary w-100">Cari</button>
                      </div>
                    </div>
                  </form>
                  <form method="GET" action="export.php" target="_blank">
                    <input type="hidden" name="kategori[]" value="<?= isset($_GET['kategori']) ? $_GET['kategori'] : '' ?>">
                  </form>

                  <table id="datatablesSimple" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Varian</th>
                            <th>Kode Barcode</th>
                            <th>Keterangan</th>
                            <th>Jumlah Keluar</th>
                            <th>Image</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

                        $where = [];
                        if (!empty($search)) {
                            $where[] = "(varian LIKE '%$search%' OR kodebarang LIKE '%$search%')";
                        }
                        if (!empty($kategori)) {
                            $where[] = "kategori = '$kategori'";
                        }

                        $finalWhere = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

                        $query = "SELECT * FROM keluar $finalWhere ORDER BY tanggal DESC";
                        $ambilsemuadatastock = mysqli_query($conn, $query);

                        if (!$ambilsemuadatastock) {
                            die("Query Error: " . mysqli_error($conn));
                        }

                        $i = 1;
                        while ($data = mysqli_fetch_array($ambilsemuadatastock)) {
                            $tanggal = date('d-m-Y', strtotime($data['tanggal']));
                            $kategori = $data['kategori'];
                            $varian = $data['varian'];
                            $kodebarang = $data['kodebarang'];
                            $keterangan = isset($data['keterangan']) ? $data['keterangan'] : '-';
                            $jumlahkeluar = $data['jumlahkeluar'];
                            $idb = $data['idbarang'];
                        ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $tanggal; ?></td>
                                <td><?= $kategori; ?></td>
                                <td><?= $varian; ?></td>
                                <td><?= $kodebarang; ?></td>
                                <td><?= $keterangan; ?></td>
                                <td><?= $jumlahkeluar; ?></td>
                                <td>
                                <?php 
                                  $imgPath = "img/" . $kodebarang . ".png";
                                  if (file_exists($imgPath)) {
                                    echo '<img src="' . $imgPath . '" alt="' . $kodebarang . '" style="width:50px; cursor:pointer;" onclick="showImageInModal(\'' . $imgPath . '\')">';
                                  } else {
                                    echo 'No image';
                                  }
                                ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete<?= $idb; ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                                  <!-- Modal Gambar -->
                                <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="imageModalLabel">Foto Produk</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                      </div>
                                      <div class="modal-body text-center">
                                        <img id="modalImage" src="" alt="Foto Produk" class="img-fluid">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                
                            <div class="modal fade" id="delete<?= $idb; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Hapus Barang</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                Yakin Ingin Hapus <?= $varian; ?>?
                                                <input type="hidden" name="idb" value="<?= $idb; ?>">
                                                <br>
                                                <button type="submit" class="btn btn-danger" name="deletekeluar">Hapus</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </tbody>
                  </table>
              </div>
          </div>
      </div>
  </main>
</div>
<!-- Modal -->
<div class="modal fade" id="modalScanKeluar" tabindex="-1" aria-labelledby="modalScanKeluarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Kelola Barang Keluar (Scan / Upload)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex justify-content-center gap-3 mb-4">
          <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#scanSection" aria-expanded="false" aria-controls="scanSection">🔍 Scan</button>
          <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#uploadSection" aria-expanded="false" aria-controls="uploadSection">📁 Upload</button>
        </div>

        <!-- SCAN SECTION -->
        <div class="collapse" id="scanSection">
          <h6 class="text-primary">Mode Scan Barcode</h6>
          <div id="scanner-container" class="border rounded mb-3" style="width: 100%; height: 300px;"></div>

          <form id="formScanKeluar" method="post" action="barang_keluar_scan.php">
            <div class="mb-3">
              <label for="barcode" class="form-label">Barcode</label>
              <input type="text" class="form-control" id="barcode" name="barcode" readonly required>
            </div>
            <div class="mb-3">
              <label for="stock" class="form-label">Stock (Qty Keluar)</label>
              <input type="number" class="form-control" id="stock" name="stock" min="1" required>
            </div>
            <div class="mb-3">
              <label for="keterangan" class="form-label">Keterangan</label>
              <input type="text" class="form-control" name="keterangan" id="keterangan">
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-success">Simpan Scan</button>
            </div>
          </form>
        </div>

        <!-- UPLOAD SECTION -->
        <div class="collapse" id="uploadSection">
          <h6 class="text-secondary">Mode Upload File</h6>
          <form action="barang_keluar_scan.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="barcode_file" class="form-label">File Barcode (.txt / .csv)</label>
              <input type="file" class="form-control" name="barcode_file" id="barcode_file" accept=".txt,.csv" required>
            </div>
            <div class="mb-3">
              <label for="keterangan" class="form-label">Keterangan</label>
              <input type="text" class="form-control" name="keterangan" id="keterangan">
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Upload dan Proses</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Panggil file JS Quagga dan file JS terpisah -->
<script src="https://unpkg.com/quagga/dist/quagga.min.js"></script>
<script src="js/scan_keluar.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'layout/footer.php'; ?>
