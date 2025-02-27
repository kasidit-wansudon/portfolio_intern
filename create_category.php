<?php
include 'config.php';
session_start();

// ตรวจสอบ login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$alertMessage = "";  // เก็บข้อความสำหรับแจ้งผล SweetAlert

// ถ้ามีการส่งฟอร์มเพิ่มหมวดหมู่
if (isset($_POST['add_category'])) {
  $category_name = $_POST['category_name'];

  // 1) เช็คว่ามีชื่อหมวดหมู่ซ้ำใน DB หรือไม่
  $checkSql = "SELECT * FROM categories WHERE category_name = '$category_name' LIMIT 1";
  $checkRes = mysqli_query($conn, $checkSql);

  if (mysqli_num_rows($checkRes) > 0) {
    // ถ้าเจอว่ามีซ้ำ
    // เก็บข้อความเพื่อไปแสดง SweetAlert
    $alertMessage = "duplicate";
  } else {
    // 2) ถ้าไม่ซ้ำ => INSERT
    $sql = "INSERT INTO categories (category_name) VALUES ('$category_name')";
    mysqli_query($conn, $sql);

    // เก็บข้อความเพื่อไปแสดง SweetAlert
    $alertMessage = "success";
  }
}

// ดึงรายชื่อหมวดหมู่ทั้งหมด
$catSQL   = "SELECT * FROM categories ORDER BY id DESC";
$catQuery = mysqli_query($conn, $catSQL);
?>
<!DOCTYPE html>
<html>
<head>
  <title>สร้างหมวดหมู่</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
  <!-- SweetAlert2 -->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body> 
  <?php include 'navbar.php'; ?> <!-- เรียกใช้ Navbar -->

  <div class="container mt-4">
    <h2>สร้างหมวดหมู่ใหม่ 📝</h2>
    <form method="post" action="">
      <div class="mb-3">
        <label for="category_name" class="form-label">ชื่อหมวดหมู่</label>
        <input type="text" name="category_name" class="form-control" id="category_name" required>
      </div>
      <button type="submit" name="add_category" class="btn btn-primary">สร้าง</button>
    </form>

    <hr>
    <h3>รายชื่อหมวดหมู่ทั้งหมด 📌</h3>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>ชื่อหมวดหมู่</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($cat = mysqli_fetch_assoc($catQuery)): ?>
          <tr>
            <td><?php echo $cat['id']; ?></td>
            <td><?php echo $cat['category_name']; ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- สคริปต์ SweetAlert2 สำหรับแจ้งผล -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      let alertMessage = "<?php echo $alertMessage; ?>";

      if (alertMessage === "duplicate") {
        Swal.fire({
          icon: 'error',
          title: 'ชื่อหมวดหมู่ซ้ำ!',
          text: 'ไม่สามารถเพิ่มหมวดหมู่ชื่อเดียวกันได้',
          confirmButtonText: 'ตกลง'
        });
      }
      else if (alertMessage === "success") {
        Swal.fire({
          icon: 'success',
          title: 'เพิ่มหมวดหมู่สำเร็จ!',
          text: 'สร้างหมวดหมู่ใหม่เรียบร้อย ✅',
          confirmButtonText: 'เยี่ยม!'
        });
      }
    });
  </script>
</body>
</html>
