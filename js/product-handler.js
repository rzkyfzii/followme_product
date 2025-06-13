// function isiKodeBarang() {
//   const varian = document.getElementById('varian').value;
//   const kodebarangInput = document.getElementById('kodebarang');

//   const kodeBarangMap = {
    
//     'GLORIOUS MOONLIGHT': '1001',
//     'L AME DE L OCEAN': '1002',
//     'SENSO DI BLOSSOM': '1003',
//     'SUNSET FALVOR': '1004',

//     'AMBER VOUGERE': '2001',
//     'AMETHYST': '2002',
//     'BROTHERHOOD STORY': '2003',
//     'HAPPINESS': '2004',
//     'OMBRE': '2005',
//     'ROSES': '2006',
//     'JASMINUM SAMBAC': '2007',
//     'LOVE OUD': '2008',
//     'MAGIC OF NATURE': '2009',
//     'MATCHER': '2010',

//     'MEN PERFUME (RED)': '3001',
//     'MEN PERFUME (WHITE)': '3002',
//     'MEN PERFUME (YELLOW)': '3003',
//     'SENCE AMETHYST': '3004',
//     'SENCE HAPPY': '3005',
//     'SENCE JOYFUL': '3006',
//     'SENCE LOVELY': '3007',
//     'SENCE ROMANCE': '3008',
//     'SENCE SECRET': '3009',

//     'MEN BODY SPRAY RED': '4001',
//     'MEN BODY SPRAY (WHITE)': '4002',
//     'MEN BODY SPRAY (YELLOW)': '4003',
//     'SENCE BODY SPRAY HAPPY': '4004',
//     'SENCE BODY SPRAY JOYFUL': '4005',
//     'SENCE BODY SPRAY LOVELY': '4006',
//     'SENCE BODY SPRAY ROMANCE': '4007',

//     'DISFFUSER FLORAL SENSATION' : '5001',
//     'DISFFUSER WARM TOBACCO'     : '5002',
//     'DISFFUSER WOODY'            : '5003',
//     'DISFFUSER WHITE'            : '5004',
//     'DISFFUSER BLUE'             : '5005',
//     'DISFFUSER'                  : '5006',

//     'HAIR CREAM (BLACK)'         : '6001',
//     'HAIR CREAM (WHITE)'         : '6002',
//     'SUPER HARD GEL (PURPLE)'    : '6003',
//     'SUPER HARD GEL (GREEN FIX)' : '6004',
//     'UPER HARD GEL (BLUE)'       : '6005'
//   };

//   if (varian && kodeBarangMap[varian]) {
//     kodebarangInput.value = kodeBarangMap[varian];
//   } else {
//     kodebarangInput.value = '';
//   }
// }
document.addEventListener('DOMContentLoaded', function () {
  const scannerContainer = document.getElementById("scanner-container");
  const scanResult = document.getElementById("scan-result");
  const inputHidden = document.getElementById("barcode_scanned");
  const modal = document.getElementById("myModal");

  if (!scannerContainer || !modal) return;

  let isScannerRunning = false;

  const startScanner = () => {
    if (isScannerRunning) return;

    Quagga.init({
      inputStream: {
  type: "LiveStream",
  target: scannerContainer,
  constraints: {
    width: { ideal: 640 },
    height: { ideal: 480 },
    facingMode: "environment",
    advanced: [{ focusMode: "continuous" }] // auto fokus
  }


      },
    locator: {
  patchSize: "large", // atau "x-large"
  halfSample: true
},
locate: true,

      decoder: {
        readers: ["ean_reader", "code_128_reader"]
      }
    }, function (err) {
      if (err) {
        console.error("Quagga init error:", err);
        alert("Gagal mengakses kamera: " + err.name);
        return;
      }
      Quagga.start();
      isScannerRunning = true;
    });

    Quagga.onDetected(onDetectedOnce);
  };

  const onDetectedOnce = (data) => {
    const code = data.codeResult.code;

    // validasi sederhana panjang barcode
    if (!code || code.length < 8) return;

    if (scanResult) scanResult.innerText = "Barcode Terdeteksi: " + code;
    if (inputHidden) inputHidden.value = code;

    Quagga.stop();
    isScannerRunning = false;
    Quagga.offDetected(onDetectedOnce); // matikan event listener
  };

  modal.addEventListener('shown.bs.modal', startScanner);

  modal.addEventListener('hidden.bs.modal', function () {
    if (isScannerRunning) {
      Quagga.stop();
      isScannerRunning = false;
    }
    Quagga.offDetected(onDetectedOnce); // pastikan listener tidak berlipat
    if (scanResult) scanResult.innerText = '';
    if (inputHidden) inputHidden.value = '';
  });
});

document.addEventListener('DOMContentLoaded', function () {
  const scannerContainer = document.getElementById("scanner-container");
  const barcodeInput = document.getElementById("barcode");
  const modal = document.getElementById("modalScanKeluar");
  const stockInput = document.getElementById("stock");

  if (!scannerContainer || !modal) return;

  let isScannerRunning = false;

  const startScanner = () => {
    if (isScannerRunning) return;

    // Pastikan mediaDevices tersedia
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      alert("Browser tidak mendukung kamera.");
      return;
    }

    Quagga.init({
      inputStream: {
        type: "LiveStream",
        target: scannerContainer,
        constraints: {
          width: { ideal: 640 },
          height: { ideal: 480 },
          facingMode: "environment",
          advanced: [{ focusMode: "continuous" }]
        }
      },
      locator: {
        patchSize: "large",
        halfSample: true
      },
      locate: true,
      decoder: {
        readers: ["ean_reader", "code_128_reader"]
      }
    }, function (err) {
      if (err) {
        console.error("Quagga init error:", err);
        alert("Gagal mengakses kamera: " + err.name);
        return;
      }
      Quagga.start();
      isScannerRunning = true;
    });

    Quagga.onDetected(onDetectedOnce);
  };

  const onDetectedOnce = (data) => {
    const code = data.codeResult.code;
    if (!code || code.length < 8) return;

    barcodeInput.value = code;
    stockInput.focus();

    Quagga.stop();
    isScannerRunning = false;
    Quagga.offDetected(onDetectedOnce);
  };

  modal.addEventListener('shown.bs.modal', function () {
    setTimeout(startScanner, 500); // beri delay agar DOM siap
  });

  modal.addEventListener('hidden.bs.modal', function () {
    if (isScannerRunning) {
      Quagga.stop();
      isScannerRunning = false;
    }
    Quagga.offDetected(onDetectedOnce);

    barcodeInput.value = '';
    stockInput.value = '';
  });
});


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

