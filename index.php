<?php
include 'config.php';
session_start();

// ดึงข้อมูลไฟล์ทั้งหมดจากตาราง uploads
$filesSQL   = "SELECT * FROM uploads ORDER BY uploaded_at DESC";
$filesQuery = mysqli_query($conn, $filesSQL);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Home Page</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>  <!-- เรียกใช้ Navbar -->

<div class="container">

  <h2>หน้าแรก (Home)</h2>
  
  <!-- ลิงก์ไปหน้า Upload / หน้าแสดงไฟล์ / หน้า Login -->
  <div class="mb-3">
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="upload.php" class="btn btn-primary">อัปโหลดไฟล์</a>
      <a href="show_files.php" class="btn btn-warning">ดูไฟล์ (Filter)</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-success">เข้าสู่ระบบ</a>
      <a href="register.php" class="btn btn-secondary">สมัครสมาชิก</a>
    <?php endif; ?>
  </div>
  
  <!-- ตารางแสดงไฟล์ทั้งหมด -->
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ชื่อไฟล์</th>
        <th>ดู/ดาวน์โหลด</th>
        <th>อัปโหลดเมื่อ</th>
      </tr>
    </thead>
    <tbody>
      <?php while($file = mysqli_fetch_assoc($filesQuery)): ?>
      <tr>
        <td><?php echo $file['file_name']; ?></td>
        <td><a href="<?php echo $file['file_path']; ?>" target="_blank">Open/Download</a></td>
        <td><?php echo $file['uploaded_at']; ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</div>
</body>
</html>
