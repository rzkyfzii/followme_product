<?php
require 'function.php';  // koneksi, fungsi, dll

include 'auth.php';      // cek login, session_start() harus di sini

include 'layout/header.php';   // output HTML dimulai dari sini
include 'layout/sidebar.php';
?>

<h1>Welcome to Dashboard</h1>

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
