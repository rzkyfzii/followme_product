<?php require('function.php'); ?>
<?php require ('cek.php');?>
<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<h2>SANJU</h2>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <div class="card mb-4">
              <div class="card-header d-flex align-items-center">
              <button type="button" class="btn btn-primary me-2" data-toggle="modal" data-target="#myModal">
                Tambah Barang
              </button>
              <form method="GET" action="export/export_sanju.php" target="_blank" class="mb-0">
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
                          <div class="col-md-5">
                              <input type="text" class="form-control" name="search" placeholder="Cari Barang" value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>" />
                          </div>
                          <div class="col-md-4">
                              <select name="lokasi" class="form-control">
                                  <option value="">Semua Gudang</option>
                                  <option value="Gudang A" <?= (isset($_GET['lokasi']) && $_GET['lokasi'] == 'Gudang A') ? 'selected' : ''; ?>>Gudang A</option>
                                  <option value="Gudang C" <?= (isset($_GET['lokasi']) && $_GET['lokasi'] == 'Gudang C') ? 'selected' : ''; ?>>Gudang C</option>
                              </select>
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
                                <th>Kategori</th>
                                <th>Varian</th>
                                <th>Kode Barang</th>
                                <th>Stock</th>
                                <th>Lokasi</th>
                                <th>image</th>
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

                            $query = "SELECT * FROM sanju $finalWhere ORDER BY tanggal DESC";
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
                                $lokasi = $data['lokasi'];
                                $idb = $data['idbarang'];
                            ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= $tanggal; ?></td>
                                    <td><?= $kategori; ?></td>
                                    <td><?= $varian; ?></td>
                                    <td><?= $kodebarang; ?></td>
                                    <td><?= $stock; ?></td>
                                    <td><?= $lokasi; ?></td>
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
                                                    <button type="submit" class="btn btn-danger" name="deletesanju">Hapus</button>
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

 <!-- The Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Tambah Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form method="POST" class="custom-form">

        <div class="form-group">
        <label for="kategori">Kategori:</label>
        <select class="form-control" id="kategori" name="kategori" required>
          <option value="">-- Pilih Kategori --</option>
          <option value="Parfum">Parfum</option>
          <option value="Aerosols">Aerosols</option>
          <option value="Homecare">Homecare</option>
          <option value="Haircare">Haircare</option>
        </select>
      </div>

          <div class="form-group">
            <label for="varian">Pilih Varian:</label>
            <select class="form-control" id="varian" name="varian" onchange="isiKodeBarang()" required>
              <option value="">-- Pilih Varian --</option>
              <option value="MEN PERFUME (RED)">MEN PERFUME (RED)</option>
              <option value="MEN PERFUME (WHITE)">MEN PERFUME (WHITE)</option>
              <option value="MEN PERFUME (YELLOW)">MEN PERFUME (YELLOW)</option>
              <option value="SENCE AMETHYST">SENCE AMETHYST</option>
              <option value="SENCE HAPPY">SENCE HAPPY</option>
              <option value="SENCE JOYFUL">SENCE JOYFUL</option>
              <option value="SENCE LOVELY">SENCE LOVELY</option>
              <option value="SENCE ROMANCE">SENCE ROMANCE</option>
              <option value="SENCE SECRET">SENCE SECRET</option>
            </select>
          </div>

          <div class="form-group">
            <label for="kodebarang">Kode Barang:</label>
            <input type="text" class="form-control" id="kodebarang" name="kodebarang" readonly required>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="stock">Stock</label>
              <input type="number" class="form-control" name="stock" id="stock" required min="1">
            </div>
            <div class="form-group col-md-6">
            <label for="lokasi">Lokasi</label>
            <select class="form-control" name="lokasi" id="lokasi" required>
              <option value="">-- Pilih Gudang --</option>
              <option value="Gudang A">Gudang A</option>
              <option value="Gudang C">Gudang C</option>
            </select>
          </div>

          </div>

          <!-- Modal Footer -->
          <div class="modal-footer p-0 pt-3">
            <button type="submit" name="sanju" class="btn btn-primary">Simpan</button>
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
