document.addEventListener('DOMContentLoaded', function () {
  const scannerContainer = document.getElementById("scanner-container");
  const barcodeInput = document.getElementById("barcode");
  const modal = document.getElementById("modalScanKeluar");
  const stockInput = document.getElementById("stock");

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

  modal.addEventListener('shown.bs.modal', startScanner);

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
