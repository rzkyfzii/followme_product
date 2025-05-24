function isiKodeBarang() {
  const varian = document.getElementById('varian').value;
  const kodebarangInput = document.getElementById('kodebarang');

  const kodeBarangMap = {
    
    'GLORIOUS MOONLIGHT': '1001',
    'L AME DE L OCEAN': '1002',
    'SENSO DI BLOSSOM': '1003',
    'SUNSET FALVOR': '1004',

    'AMBER VOUGERE': '2001',
    'AMETHYST': '2002',
    'BROTHERHOOD STORY': '2003',
    'HAPPINESS': '2004',
    'OMBRE': '2005',
    'ROSES': '2006',
    'JASMINUM SAMBAC': '2007',
    'LOVE OUD': '2008',
    'MAGIC OF NATURE': '2009',
    'MATCHER': '2010',

    'MEN PERFUME (RED)': '3001',
    'MEN PERFUME (WHITE)': '3002',
    'MEN PERFUME (YELLOW)': '3003',
    'SENCE AMETHYST': '3004',
    'SENCE HAPPY': '3005',
    'SENCE JOYFUL': '3006',
    'SENCE LOVELY': '3007',
    'SENCE ROMANCE': '3008',
    'SENCE SECRET': '3009',

    'MEN BODY SPRAY RED': '4001',
    'MEN BODY SPRAY (WHITE)': '4002',
    'MEN BODY SPRAY (YELLOW)': '4003',
    'SENCE BODY SPRAY HAPPY': '4004',
    'SENCE BODY SPRAY JOYFUL': '4005',
    'SENCE BODY SPRAY LOVELY': '4006',
    'SENCE BODY SPRAY ROMANCE': '4007',

    'DISFFUSER FLORAL SENSATION' : '5001',
    'DISFFUSER WARM TOBACCO'     : '5002',
    'DISFFUSER WOODY'            : '5003',
    'DISFFUSER WHITE'            : '5004',
    'DISFFUSER BLUE'             : '5005',
    'DISFFUSER'                  : '5006',

    'HAIR CREAM (BLACK)'         : '6001',
    'HAIR CREAM (WHITE)'         : '6002',
    'SUPER HARD GEL (PURPLE)'    : '6003',
    'SUPER HARD GEL (GREEN FIX)' : '6004',
    'UPER HARD GEL (BLUE)'       : '6005'
  };

  if (varian && kodeBarangMap[varian]) {
    kodebarangInput.value = kodeBarangMap[varian];
  } else {
    kodebarangInput.value = '';
  }
}



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

  const exportBtn = document.getElementById('exportPdfBtn');
  const lokasiSelect = document.querySelector('select[name="lokasi"]');

  lokasiSelect.addEventListener('change', updateExportUrl);
  document.addEventListener('DOMContentLoaded', updateExportUrl); // Update saat halaman pertama kali dimuat

  function updateExportUrl() {
    const lokasi = lokasiSelect.value;
    let url = 'export.php';

    if (lokasi) {
      url += '?lokasi[]=' + encodeURIComponent(lokasi);
    }

    exportBtn.href = url;
  }


  function showImageInModal(src) {
    document.getElementById('modalImage').src = src;
    var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
    myModal.show();
  }

  document.getElementById('kategoriSelect').addEventListener('change', function () {
    var inputBaru = document.getElementById('kategoriBaruInput');
    if (this.value === 'lainnya') {
      inputBaru.classList.remove('d-none');
      inputBaru.setAttribute('required', 'required');
    } else {
      inputBaru.classList.add('d-none');
      inputBaru.removeAttribute('required');
    }
  });


// Referensi elemen form
const varianSelect = document.getElementById('varian');
const kodeBarangInput = document.getElementById('kodebarang');
const kategoriInput = document.getElementById('kategori');
const gambarPreviewDiv = document.getElementById('gambar-preview');

// Isi opsi varian saat halaman siap
function isiVarian() {
  produkBaru.forEach(produk => {
    const option = document.createElement('option');
    option.value = produk.varian;
    option.textContent = produk.varian;
    varianSelect.appendChild(option);
  });
}

// Fungsi update kodebarang, kategori dan gambar saat varian dipilih
function updateDetailBarang() {
  const selectedVarian = varianSelect.value;
  const produk = produkBaru.find(p => p.varian === selectedVarian);
  if (produk) {
    kodeBarangInput.value = produk.kodebarang;
    kategoriInput.value = produk.kategori;

    // Tampilkan gambar
    if (produk.gambar) {
      gambarPreviewDiv.innerHTML = `<img src="path/to/gambar/${produk.gambar}" alt="Gambar Produk" class="img-fluid">`;
    } else {
      gambarPreviewDiv.innerHTML = '';
    }
  } else {
    kodeBarangInput.value = '';
    kategoriInput.value = '';
    gambarPreviewDiv.innerHTML = '';
  }
}

// Event listener varian berubah
varianSelect.addEventListener('change', updateDetailBarang);

// Jalankan fungsi isiVarian saat halaman siap
document.addEventListener('DOMContentLoaded', isiVarian);

  document.addEventListener('DOMContentLoaded', function () {
    // Ketika modal dibuka, panggil fungsi loadVarian()
    var myModal = document.getElementById('myModal');
    myModal.addEventListener('show.bs.modal', loadVarian);

    function loadVarian() {
        fetch('get_varian.php')
            .then(response => response.json())
            .then(data => {
                var selectVarian = document.getElementById('varian');
                selectVarian.innerHTML = '<option value="">-- Pilih Varian --</option>'; // reset options

                data.forEach(function(item) {
                    var option = document.createElement('option');
                    option.value = item.idbarang; // bisa pakai idbarang sebagai value
                    option.textContent = item.varian;
                    // Simpan data lain sebagai atribut data-* untuk nanti dipakai autofill
                    option.dataset.kodebarang = item.kodebarang;
                    option.dataset.kategori = item.kategori;
                    option.dataset.gambar = item.gambar;
                    selectVarian.appendChild(option);
                });
            })
            .catch(err => console.error('Error load varian:', err));
    }

    // Ketika varian dipilih, autofill kodebarang, kategori, dan gambar
    document.getElementById('varian').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        document.getElementById('kodebarang').value = selectedOption.dataset.kodebarang || '';
        document.getElementById('kategori').value = selectedOption.dataset.kategori || '';

        var gambarFile = selectedOption.dataset.gambar || '';
        var previewDiv = document.getElementById('gambar-preview');
        if (gambarFile) {
            previewDiv.innerHTML = '<img src="img/' + gambarFile + '" alt="Gambar Produk" style="max-width: 150px;">';
        } else {
            previewDiv.innerHTML = '';
        }
    });
});


function isivarian() {
  const kategori = document.getElementById("kategori").value;
  const varianSelect = document.getElementById("varian");

  // Kosongkan isi dropdown varian
  varianSelect.innerHTML = '<option value="">-- Pilih varian --</option>';

  if (!kategori) return;

  fetch("get_varian_by_kategori.php?kategori=" + encodeURIComponent(kategori))
    .then(response => response.json())
    .then(data => {
      data.forEach(item => {
        const option = document.createElement("option");
        option.text = `${item.varian}`;
        varianSelect.appendChild(option);
      });
    })
    .catch(err => {
      console.error("Gagal mengambil varian:", err);
    });
}


