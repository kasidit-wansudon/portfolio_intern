<?php
include 'config.php';
session_start();

// ตรวจสอบว่าสมาชิกล็อกอินแล้ว
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// ดึงหมวดหมู่ทั้งหมดจากตาราง categories
$catSQL   = "SELECT * FROM categories";
$catQuery = mysqli_query($conn, $catSQL);

if (isset($_POST['upload'])) {
  $user_id     = $_SESSION['user_id'];
  $category_id = $_POST['category_id'];
  $file        = $_FILES['file'];

  if ($file['name'] != '') {
    $fileName   = basename($file['name']);
    $targetPath = 'uploads/' . $fileName;
    
    // 1) เช็คว่ามีไฟล์ชื่อซ้ำอยู่ในโฟลเดอร์ uploads หรือไม่
    if (file_exists($targetPath)) {
      echo "<div class='alert alert-danger mt-3'>⚠️ มีไฟล์ชื่อนี้อยู่แล้ว ไม่สามารถอัพโหลดซ้ำได้!</div>";
    } else {
      // 2) ถ้าไม่ซ้ำ จึงค่อย move_uploaded_file
      if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $insertSQL = "INSERT INTO uploads (user_id, category_id, file_name, file_path) 
                      VALUES ('$user_id', '$category_id', '$fileName', '$targetPath')";
        mysqli_query($conn, $insertSQL);
        echo "<div class='alert alert-success mt-3'>อัพโหลดไฟล์เรียบร้อย 🎉</div>";
      } else {
        echo "<div class='alert alert-warning mt-3'>อัพโหลดไฟล์ไม่สำเร็จ ลองอีกครั้ง!</div>";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Upload File</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
</head>

<body>
  
 <?php include 'navbar.php'; ?>  <!-- เรียกใช้ Navbar -->

  <div class="container">
    <h2 class="mb-4">อัปโหลดไฟล์</h2>
    <form method="post" action="" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="category_id" class="form-label">เลือกหมวดหมู่</label>
        <select name="category_id" class="form-select" required>
          <option value="">-- เลือก --</option>
          <?php while ($cat = mysqli_fetch_assoc($catQuery)): ?>
            <option value="<?php echo $cat['id']; ?>">
              <?php echo $cat['category_name']; ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="file" class="form-label">เลือกไฟล์</label>
        <input type="file" name="file" class="form-control" required>
      </div>

      <button type="submit" name="upload" class="btn btn-primary">อัปโหลด</button>
    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
