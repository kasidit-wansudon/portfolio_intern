<?php
include 'config.php';
session_start();

// ถ้าไม่ได้ล็อกอิน ให้ไปหน้า login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// ดึงหมวดหมู่ทั้งหมด
$catSQL   = "SELECT * FROM categories";
$catQuery = mysqli_query($conn, $catSQL);

// ตัวแปรสำหรับ SweetAlert
$message = "";
$msgType = ""; // success, error, warning

if (isset($_POST['upload'])) {
  $user_id     = $_SESSION['user_id'];
  $category_id = $_POST['category_id'];
  $file        = $_FILES['file'];

  if ($file['name'] != '') {
    $fileName   = basename($file['name']);
    $targetPath = 'uploads/' . $fileName;

    // เช็คชื่อไฟล์ซ้ำ
    if (file_exists($targetPath)) {
      $msgType = "error";
      $message = "⚠️ มีไฟล์ชื่อนี้อยู่แล้ว ไม่สามารถอัพโหลดซ้ำได้!";
    } else {
      if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // บันทึกลงฐานข้อมูล
        $insertSQL = "INSERT INTO uploads (user_id, category_id, file_name, file_path) 
                      VALUES ('$user_id', '$category_id', '$fileName', '$targetPath')";
        mysqli_query($conn, $insertSQL);

        $msgType = "success";
        $message = "อัพโหลดไฟล์เรียบร้อย 🎉";
      } else {
        $msgType = "warning";
        $message = "อัพโหลดไฟล์ไม่สำเร็จ ลองอีกครั้ง!";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Upload File</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
  
  <!-- SweetAlert2 -->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- สไตล์เรียบง่าย -->
  <style>
    body {
      background-color: #f7f7f7;
      font-family: "Prompt", sans-serif;
    }
    .upload-container {
      max-width: 500px;
      margin: 50px auto;
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 2rem;
    }
    .btn-upload {
      background-color: #4caf50;
      border: none;
    }
    .btn-upload:hover {
      background-color: #43a047;
    }
  </style>
</head>

<body>
  <!-- Navbar ถ้ามี -->
  <?php include 'navbar.php'; ?>

  <div class="upload-container">
    <h4 class="mb-4 text-center">อัปโหลดไฟล์</h4>
    <form method="post" action="" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="category_id" class="form-label fw-bold">เลือกหมวดหมู่</label>
        <select name="category_id" id="category_id" class="form-select" required>
          <option value="">-- เลือก --</option>
          <?php while ($cat = mysqli_fetch_assoc($catQuery)): ?>
            <option value="<?php echo $cat['id']; ?>">
              <?php echo $cat['category_name']; ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="file" class="form-label fw-bold">เลือกไฟล์</label>
        <input type="file" name="file" id="file" class="form-control" required>
      </div>

      <button type="submit" name="upload" class="btn btn-upload text-white w-100">
        อัปโหลด
      </button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // SweetAlert2 แจ้งเตือน
    document.addEventListener("DOMContentLoaded", function(){
      let msgType = "<?php echo $msgType; ?>";
      let message = "<?php echo $message; ?>";
      if(message !== "") {
        Swal.fire({
          icon: msgType, // 'success', 'error', 'warning', 'info'
          title: message,
          confirmButtonText: 'ตกลง',
          timer: 3000
        });
      }
    });
  </script>
</body>
</html>
