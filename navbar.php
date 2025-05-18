<?php
// navbar.php

// ดึงชื่อผู้ใช้จาก session ถ้ามี
$username = "";
if (isset($_SESSION['user_id'])) {
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : "";
}

// ตรวจสอบหน้าปัจจุบัน
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- เริ่ม Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark custom-navbar shadow-sm sticky-top">
  <div class="container-fluid">
    <!-- Brand / Logo -->
    <a class="navbar-brand fw-bold" href="index.php">
      <i class="bi bi-folder2-open me-1"></i> My File Management
    </a>

    <!-- Toggle button (mobile) -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- เมนู -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Left menu -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link px-3 <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">หน้าแรก</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 <?php echo ($current_page == 'upload.php') ? 'active' : ''; ?>" href="upload.php">อัปโหลดไฟล์</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 <?php echo ($current_page == 'show_files.php') ? 'active' : ''; ?>" href="show_files.php">ดูไฟล์ทั้งหมด</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 <?php echo ($current_page == 'create_category.php') ? 'active' : ''; ?>" href="create_category.php">สร้างหมวดหมู่</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 <?php echo ($current_page == 'manage_admin.php') ? 'active' : ''; ?>" href="manage_admin.php">Manage Admin</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 <?php echo ($current_page == 'gen_qr.php') ? 'active' : ''; ?>" href="gen_qr.php">
            สร้าง QR Code
          </a>
        </li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <a class="nav-link px-3 <?php echo ($current_page == 'user_history.php') ? 'active' : ''; ?>" href="user_history.php">
              ประวัติการเข้าออกงาน
            </a>
          </li>
        <?php endif; ?>

      </ul>

      <!-- Right menu: User info & ปุ่มพิเศษ -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item d-flex align-items-center me-3">
            <span class="user-greeting">
              สวัสดี, <?php echo htmlspecialchars($username); ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="btn btn-outline-light me-2 nav-btn" href="logout.php">
              <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
          </li>
          <li class="nav-item">
            <a class="btn btn-info nav-btn" href="send_report.php">
              <i class="bi bi-envelope-paper me-1"></i> ส่งรายงาน
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="btn btn-outline-light me-2 nav-btn" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-info nav-btn" href="register.php">Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Bootstrap Icons (for extra icons) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Custom CSS สำหรับ Navbar -->
<style>
  .custom-navbar {
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    transition: background 0.5s ease;
  }

  .nav-link {
    transition: color 0.3s ease;
    font-size: 1rem;
  }

  .nav-link:hover {
    color: #ffc107 !important;
  }

  .nav-btn {
    transition: background-color 0.3s ease, transform 0.3s ease;
  }

  .nav-btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
  }

  .user-greeting {
    color: #fff;
    font-size: 0.95rem;
  }
</style>

<!-- JavaScript ลูกเล่นเล็กๆ เมื่อ scroll -->
<script>
  window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.custom-navbar');
    if (window.scrollY > 50) {
      navbar.style.background = 'linear-gradient(135deg, #0b5ed7, #520dc2)';
    } else {
      navbar.style.background = 'linear-gradient(135deg, #0d6efd, #6610f2)';
    }
  });
</script>

<!-- เพิ่มพื้นที่ด้านล่าง Navbar -->
<div class="py-3"></div>