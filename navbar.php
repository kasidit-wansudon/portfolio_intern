<?php
// navbar.php

// เริ่ม session ตรงนี้ได้ถ้ายังไม่เริ่มในหน้าอื่น
// session_start(); // ไม่จำเป็นถ้าทุกหน้า include ก่อนแล้ว

// สมมติว่าชื่อผู้ใช้เก็บในตาราง users, มีค่า username
// เราจะดึงจาก DB หรือ $_SESSION['username'] ก็ได้
// แต่กรณีในโค้ดตัวอย่างระบบ login ก่อนหน้า เราเก็บ user_id ใน session
// จึงต้อง Query ดึง username เพิ่ม หรืออาจเก็บ username ใน session เลย
$username = "";
if(isset($_SESSION['user_id'])) {
    // ตัวอย่าง: Query หรือตั้ง session ไว้ตั้งแต่ตอน login
    // $uid = $_SESSION['user_id'];
    // $res = mysqli_query($conn, "SELECT username FROM users WHERE id='$uid'");
    // $row = mysqli_fetch_assoc($res);
    // $username = $row['username'];

    // ถ้าเคยตั้ง $_SESSION['username'] ไว้ตั้งแต่ login ก็ใช้ได้เลย:
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : "";
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark p-2 py-4">
  <div class="container-fluid">
    <!-- Brand / Logo -->
    <a class="navbar-brand" href="index.php">
      <strong>My File Management</strong>
    </a>

    <!-- Toggle button (mobile) -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav" aria-controls="navbarNav"
            aria-expanded="false" aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Left menu -->
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">หน้าแรก</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="upload.php">อัปโหลดไฟล์</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="show_files.php">ดูไฟล์ทั้งหมด</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="create_category.php">สร้างหมวดหมู่</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="manage_admin.php">Manage Admin</a>
        </li>
      </ul>

      <!-- Right menu: User info -->
      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['user_id'])): ?>
          <!-- แสดงชื่อผู้ใช้ -->
          <li class="nav-item d-flex align-items-center me-2">
            <span class="text-white">
              สวัสดี, <?php echo htmlspecialchars($username); ?>
            </span>
          </li>
          <!-- ปุ่ม Logout -->
          <li class="nav-item">
            <a class="btn btn-outline-light" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <!-- ถ้ายังไม่ login -->
          <li class="nav-item">
            <a class="btn btn-outline-light me-2" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-info" href="register.php">Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="p-5"></div>