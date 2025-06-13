<?php
require 'function.php';  // koneksi, fungsi, dll

include 'auth.php';      // cek login, session_start() harus di sini

?>
<?php require('function.php'); ?>
<?php require ('cek.php');?>
<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<h2></h2>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <div class="card mb-4">
              <div class="card-header d-flex align-items-center">
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#myModal">
              scan Barang
            </button>
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#uploadModal">
  Upload File Barcode
</button>
              <form method="GET" action="export/export_haircare.php" target="_blank" class="mb-0">
                <!-- Kirimkan lokasi yang dipilih -->
                <input type="hidden" name="lokasi[]" value="<?= isset($_GET['lokasi']) ? $_GET['lokasi'] : '' ?>">
                
               
              </form>
            </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <form method="GET" class="mb-3">
                      <div class="row">
                          <div class="col-md-5">
                              <input type="text" class="form-control" name="search" placeholder="Cari Barang" value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>" />
                          </div>
                          <div class="col-md-4">
                              <select name="lokasi" class="form-control">
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
                      <div class="table-responsive"><table id="datatablesSimple" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Varian</th>
                                <th>Kode Barcode</th>
                                <th>Stock</th>
                                <th>image</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

                        $where = [];
                        if (!empty($search)) {
                            $search = mysqli_real_escape_string($conn, $search); // amankan dari SQL injection
                            $where[] = "(varian LIKE '%$search%' OR kodebarang LIKE '%$search%')";
                        }
                        if (!empty($kategori)) {
                            $kategori = mysqli_real_escape_string($conn, $kategori);
                            $where[] = "kategori = '$kategori'";
                        }

                        $finalWhere = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

                        $query = "
                            SELECT * FROM (
                                SELECT 'eksklusif' AS sumber, idbarang, tanggal, kategori, varian, kodebarang, stock, lokasi FROM eksklusif
                                UNION ALL
                                SELECT 'exrait' AS sumber, idbarang, tanggal, kategori, varian, kodebarang, stock, lokasi FROM exrait
                                UNION ALL
                                SELECT 'sanju' AS sumber, idbarang, tanggal, kategori, varian, kodebarang, stock, lokasi FROM sanju
                                UNION ALL
                                SELECT 'aerosols' AS sumber, idbarang, tanggal, kategori, varian, kodebarang, stock, lokasi FROM aerosols
                            ) AS gabungan
                            $finalWhere
                            ORDER BY tanggal DESC
                        ";

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
                                $stock = $data['stock'];
                                $idb = $data['idbarang'];
                            ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= $tanggal; ?></td>
                                    <td><?= $kategori; ?></td>
                                    <td><?= $varian; ?></td>
                                    <td><?= $kodebarang; ?></td>
                                    <td><?= $stock; ?></td>
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

                               

                            <?php } ?>
                        </tbody>
                    </table></div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Scanner -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">  
      <form method="POST" action="upload_scan.php" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="myModalLabel">Scan Barcode atau Upload File</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">

          <!-- SCANNER CONTAINER -->
          <style>
  #scanner-container {
    position: relative;
    width: 100%;
    height: 400px;
    background: #000;
    pointer-events: none; /* ⬅️ Tambahkan ini */
  }
  .scan-box {
    position: absolute;
    top: 25%;
    left: 10%;
    width: 80%;
    height: 30%;
    border: 2px dashed red;
    box-sizing: border-box;
    pointer-events: none;
    z-index: 10;
  }

          </style>

          <div id="scanner-container">
            <div class="scan-box"></div>
          </div>
          <div id="scan-result" class="text-success mt-2"></div>
          <input type="hidden" name="barcode_scanned" id="barcode_scanned">

        </div>
        <div class="mb-3">
            <label for="stock" class="form-label">Qty</label>
            <input type="number" name="stock" id="stock" class="form-control" min="1" value="1" required>
          </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Kirim</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Tambah Barang -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">  
      <form method="POST" action="upload_scan.php" enctype="multipart/form-data">
         <div class="modal-header">
          <h5 class="modal-title">Upload File Barcode</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="barcode_file" class="form-label">Upload File Hasil Scan (.TXT)</label>
            <input type="file" name="barcode_file" id="barcode_file" class="form-control" accept=".txt" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Upload</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://unpkg.com/@ericblade/quagga2@1.2.6/dist/quagga.js"></script>
<script src="js/product-handler.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("sidebarToggle");
    const body = document.body;
    const sidebar = document.querySelector(".sidebar");

    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener("click", function () {
        body.classList.toggle("sidebar-toggled");
        sidebar.classList.toggle("toggled");
      });
    }
  });
</script>
<?php include 'layout/footer.php'; ?>





