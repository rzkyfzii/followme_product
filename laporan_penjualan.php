<?php require('function.php'); ?>
<?php require('cek.php'); ?>
<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<h2>LAPORAN PENJUALAN</h2>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <div class="card mb-4">
              <div class="card-header d-flex align-items-center">
<!-- Tombol buka modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#lapPenjualanModal">
  Tambah Laporan Penjualan
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
                          <th>No.Resi</th>
                          <th>No.Pesanan</th>
                          <th>Kategori</th>
                          <th>Varian</th>
                          <th>Free Vial</th>
                          <th>Platform</th>
                          <th>COGS Product</th>
                          <th>Packing Charge</th>
                          <th>COGS Free Vial</th>
                          <th>Product Terjual</th>
                          <th>Net Income</th>
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

                        $query = "SELECT * FROM lap_penjualan $finalWhere ORDER BY tanggal DESC";
                        $ambilsemuadatastock = mysqli_query($conn, $query);

                        if (!$ambilsemuadatastock) {
                            die("Query Error: " . mysqli_error($conn));
                        }

                       $i = 1;
                        while ($data = mysqli_fetch_array($ambilsemuadatastock)) {
                            $tanggal = date('d-m-Y', strtotime($data['tanggal']));
                            $no_resi = $data['no_resi'];
                            $no_pesanan = $data['no_pesanan'];
                            $kategori = $data['kategori'];
                            $varian = $data['varian'];
                            $free_vial = $data['free_vial'];
                            $platform = $data['platform'];
                            $product = $data['product'];
                            $packing = $data['packing'];
                            $vial = $data['vial'];
                            $product_terjual = $data['product_terjual'];
                            $net_income = $data['net_income'];
                            $idb = $data['id']; // pastikan kolom id ada untuk hapus
                        ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $tanggal; ?></td>
                                <td><?= $no_resi; ?></td>
                                <td><?= $no_pesanan; ?></td>
                                <td><?= $kategori; ?></td>
                                <td><?= $varian; ?></td>
                                <td><?= $free_vial; ?></td>
                                <td><?= $platform; ?></td>
                                <td><?= $product; ?></td>
                                <td><?= $packing; ?></td>
                                <td><?= $vial; ?></td>
                                <td><?= $product_terjual; ?></td>
                                <td><?= $net_income; ?></td>
                                <td>
                                    <?php 
                                    $imgPath = "img/" . $varian . ".png"; // ganti jika perlu
                                    if (file_exists($imgPath)) {
                                        echo '<img src="' . $imgPath . '" alt="' . $varian . '" style="width:50px; cursor:pointer;" onclick="showImageInModal(\'' . $imgPath . '\')">';
                                    } else {
                                        echo 'No image';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?= $idb; ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Hapus -->
                            <div class="modal fade" id="delete<?= $idb; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Hapus Barang</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                Yakin ingin menghapus <?= $varian; ?>?
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
<div class="modal fade" id="lapPenjualanModal" tabindex="-1" aria-labelledby="lapPenjualanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl"><!-- Lebar lebih besar biar muat dua kolom -->
    <div class="modal-content">
      <form action="laporan_penjualan.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="lapPenjualanModalLabel">Form Laporan Penjualan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <!-- Kolom kiri -->
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="no_resi" class="form-label">No. Resi</label>
                  <input type="text" class="form-control" id="no_resi" name="no_resi" required>
                </div>
                <div class="mb-3">
                  <label for="kategori" class="form-label">Kategori</label>
                  <input type="text" class="form-control" id="kategori" name="kategori" required>
                </div>
                <div class="mb-3">
                  <label for="cogs_product" class="form-label">COGS Product</label>
                  <input type="number" step="0.01" class="form-control" id="cogs_product" name="cogs_product" required>
                </div>
                <div class="mb-3">
                  <label for="cogs_free_vial" class="form-label">COGS Free Vial</label>
                  <input type="number" step="0.01" class="form-control" id="cogs_free_vial" name="cogs_free_vial" required>
                </div>
                 <div class="mb-3">
                  <label for="no_pesanan" class="form-label">No. Pesanan</label>
                  <input type="text" class="form-control" id="no_pesanan" name="no_pesanan" required>
                </div>
                <div class="mb-3">
                  <label for="varian" class="form-label">Varian</label>
                  <input type="text" class="form-control" id="varian" name="varian" required>
                </div>
              </div>

              <!-- Kolom kanan -->
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="free_vial" class="form-label">Free Vial</label>
                  <input type="number" class="form-control" id="free_vial" name="free_vial" required>
                </div>
                <div class="mb-3">
                  <label for="platform" class="form-label">Platform</label>
                  <input type="text" class="form-control" id="platform" name="platform" required>
                </div>
                <div class="mb-3">
                  <label for="packing_charge" class="form-label">Packing Charge</label>
                  <input type="number" step="0.01" class="form-control" id="packing_charge" name="packing_charge" required>
                </div>
                <div class="mb-3">
                  <label for="product_terjual" class="form-label">Product Terjual</label>
                  <input type="number" class="form-control" id="product_terjual" name="product_terjual" required>
                </div>
                <div class="mb-3">
                  <label for="net_income" class="form-label">Net Income</label>
                  <input type="number" step="0.01" class="form-control" id="net_income" name="net_income" required>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-secondary">Reset</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Panggil file JS Quagga dan file JS terpisah -->
<script src="https://unpkg.com/quagga/dist/quagga.min.js"></script>
<script src="js/scan_keluar.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'layout/footer.php'; ?>
