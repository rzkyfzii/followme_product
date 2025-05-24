<?php require('function.php'); ?>
<?php require('cek.php'); ?>
<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<?php
$sanju = $conn->query("SELECT varian, kategori, kodebarang FROM sanju")->fetch_all(MYSQLI_ASSOC);
$exrait = $conn->query("SELECT varian, kategori, kodebarang FROM exrait")->fetch_all(MYSQLI_ASSOC);
$aerosols = $conn->query("SELECT varian, kategori, kodebarang FROM aerosols")->fetch_all(MYSQLI_ASSOC);
$dataBarang = array_merge($sanju, $exrait, $aerosols);
?>

<h2>BARANG KELUAR</h2>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <div class="card mb-4">
              <div class="card-header d-flex align-items-center">
              <button type="button" class="btn btn-primary me-2" data-toggle="modal" data-target="#myModal">
                Tambah Barang
              </button>
              <form method="GET" action="export/export_keluar.php" target="_blank" class="mb-0">
                <!-- Kirimkan lokasi yang dipilih -->
                <input type="hidden" name="lokasi[]" value="<?= isset($_GET['lokasi']) ? $_GET['lokasi'] : '' ?>">
                
                <button type="submit" class="btn btn-danger">
                  <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </button>
              </form>
            </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <form method="GET" class="mb-3">
                      <div class="row">
                          <div class="col-md-2">
      <select class="form-control" name="kategori">
        <option value="" <?= $selectedKategori === '' ? 'selected' : '' ?>>-- Pilih Kategori --</option>
        <?php foreach ($kategoriList as $kategori): ?>
          <option
            value="<?= htmlspecialchars($kategori) ?>"
            <?= $kategori == $selectedKategori ? 'selected' : '' ?>
          >
            <?= htmlspecialchars($kategori) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
                          <div class="col-md-0">
                          </div>
                          <div class="col-md-3">
                              <button type="submit" class="btn btn-secondary w-100">Cari</button>
                          </div>
                      </div>
                  </form>
                      <table id="datatablesSimple" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>kategori</th>
                                <th>Varian</th>
                                <th>Kode Barang</th>
                                <th>keterangan</th>
                                <th>Jumlah Keluar</th>
                                <th>Image</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Search query
                            $search = isset($_GET['search']) ? $_GET['search'] : '';
                            $lokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : '';

                            $where = [];

                            if (!empty($search)) {
                                $where[] = "(varian LIKE '%$search%' OR kodebarang LIKE '%$search%')";
                            }

                            if (!empty($lokasi)) {
                                $where[] = "lokasi = '$lokasi'";
                            }

                            $finalWhere = '';
                            if (!empty($where)) {
                                $finalWhere = 'WHERE ' . implode(' AND ', $where);
                            }

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
                                $keterangan= $data['keterangan'];
                                $jumlahkeluar= $data['jumlahkeluar'];
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

                                <!-- Delete Modal -->
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Form Barang Keluar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form method="POST" action="keluar.php" class="custom-form">

            <div class="form-group">
            <label for="kategori">Pilih kategori:</label>
            <select class="form-control" id="kategori" name="kategori" onchange="isivarian()" required>
              <option value="">-- Pilih kategori --</option>
              <option value="Parfum">Parfum</option>
              <option value="Body Spray">Body Spray</option>
              <option value="Home Care">Home Care</option>
            </select>
          </div>

          <div class="form-group">
            <label for="varian">Pilih varian:</label>
            <select class="form-control" id="varian" name="varian" onchange="isiKodeBarang()" required>
              <option value="">-- Pilih varian --</option>
            </select>
          </div>

          <div class="form-group">
            <label for="kodebarang">Kode Barang:</label>
            <input type="text" class="form-control" id="kodebarang" name="kodebarang" readonly required>
          </div>

          <div class="form-group">
          <label for="keterangan">Keterangan:</label>
          <input type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Masukkan keterangan" required>
        </div>

          <div class="form-group">
            <label for="jumlahkeluar">Jumlah Keluar:</label>
            <input type="number" class="form-control" id="jumlahkeluar" name="jumlahkeluar" min="1" required>
          </div>

            <label for="lokasi">Lokasi</label>
            <select class="form-control" name="lokasi" id="lokasi" required>
              <option value="">-- Pilih Gudang --</option>
              <option value="Gudang A">Gudang A</option>
              <option value="Gudang C">Gudang C</option>
            </select>
          </div>


          <!-- Modal Footer -->
          <div class="modal-footer pt-3">
            <button type="submit" name="keluar" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Batal</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>



<script src="js/product-handler.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'layout/footer.php'; ?>
