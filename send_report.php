<?php
include 'config.php';
session_start();

// ตรวจสอบว่า login แล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$message = "";
$msgType = ""; // success, error, warning

// ดึงไฟล์ที่มีอยู่ในระบบ (เฉพาะของผู้ใช้ที่ล็อกอิน) 
$uploadsSQL = "SELECT * FROM uploads WHERE user_id = '".$_SESSION['user_id']."' ORDER BY uploaded_at DESC";
$uploadsQuery = mysqli_query($conn, $uploadsSQL);

if (isset($_POST['send_report'])) {
  $subject = mysqli_real_escape_string($conn, $_POST['subject']);
  $details = mysqli_real_escape_string($conn, $_POST['details']);
  $user_id = $_SESSION['user_id'];

  // บันทึกข้อมูลรายงานลงในตาราง reports
  $insertSQL = "INSERT INTO reports (user_id, subject, details) 
                VALUES ('$user_id', '$subject', '$details')";
  
  if (mysqli_query($conn, $insertSQL)) {
    // รับ report id ที่เพิ่ง insert ลงไป
    $report_id = mysqli_insert_id($conn);

    // ตรวจสอบแนบไฟล์
    if(isset($_POST['attached_files']) && !empty($_POST['attached_files'])){
      foreach($_POST['attached_files'] as $upload_id) {
        $insertAttachmentSQL = "INSERT INTO report_attachments (report_id, upload_id)
                                VALUES ('$report_id', '$upload_id')";
        mysqli_query($conn, $insertAttachmentSQL);
      }
    }
    $msgType = "success";
    $message = "ส่งรายงานเรียบร้อย 🎉";
  } else {
    $msgType = "error";
    $message = "เกิดข้อผิดพลาดในการส่งรายงาน!";
  }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ส่งรายงาน</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
  <!-- SweetAlert2 -->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f7f7f7;
      font-family: "Prompt", sans-serif;
    }
    .report-container {
      max-width: 600px;
      margin: 50px auto;
      background-color: #ffffff;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

  <?php include 'navbar.php'; ?>

  <div class="report-container">
    <h2 class="text-center mb-4">ส่งรายงาน 📋</h2>
    <form method="post" action="">
      <div class="mb-3">
        <label for="subject" class="form-label fw-bold">หัวข้อรายงาน</label>
        <input type="text" name="subject" id="subject" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="details" class="form-label fw-bold">รายละเอียด</label>
        <textarea name="details" id="details" class="form-control" rows="6" required></textarea>
      </div>
      
      <!-- ส่วนสำหรับแนบไฟล์ที่มีอยู่ในระบบ -->
      <div class="mb-3">
        <label for="attached_files" class="form-label fw-bold">แนบไฟล์ (เลือกได้มากกว่า 1 ไฟล์)</label>
        <select name="attached_files[]" id="attached_files" class="form-select" multiple>
          <?php while($upload = mysqli_fetch_assoc($uploadsQuery)): ?>
            <option value="<?php echo $upload['id']; ?>">
              <?php echo $upload['file_name']; ?>
            </option>
          <?php endwhile; ?>
        </select>
        <div class="form-text">กด Ctrl (หรือ Command บน Mac) ค้างไว้ แล้วคลิกเพื่อเลือกหลายไฟล์</div>
      </div>
      
      <button type="submit" name="send_report" class="btn btn-primary w-100">ส่งรายงาน</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // แสดง SweetAlert2 เมื่อมีข้อความแจ้งเตือน
    document.addEventListener("DOMContentLoaded", function(){
      let msgType = "<?php echo $msgType; ?>";
      let message = "<?php echo $message; ?>";
      if(message !== ""){
        Swal.fire({
          icon: msgType,
          title: message,
          confirmButtonText: "ตกลง",
          timer: 3000
        });
      }
    });
  </script>
</body>
</html>
