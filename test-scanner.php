<!DOCTYPE html>
<html>
<head>
  <title>Test QuaggaJS</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
</head>
<body>
  <h2>Scan Test</h2>
  <div id="scanner-container" style="width: 100%; height: 300px; border:1px solid #ccc;"></div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const container = document.getElementById('scanner-container');

      Quagga.init({
        inputStream: {
          type: "LiveStream",
          target: container,
          constraints: {
            width: 640,
            height: 480,
            facingMode: "environment"
          }
        },
        locator: {
          patchSize: "medium",
          halfSample: true
        },
        locate: true,
        decoder: {
          readers: ["ean_reader", "code_128_reader"]
        }
      }, function (err) {
        if (err) {
          console.error(err);
          alert("Gagal inisialisasi kamera: " + err.message);
          return;
        }
        Quagga.start();
        console.log("Scanner dimulai");
      });

      Quagga.onDetected(result => {
        console.log("Barcode:", result.codeResult.code);
      });
    });
  </script>
</body>
</html>
