<?php
include 'config.php';
session_start();

// ดึงข้อมูลไฟล์ทั้งหมดจากตาราง uploads
$filesSQL   = "SELECT uploads.*,users.username FROM uploads JOIN users ON uploads.user_id = users.id  ORDER BY uploaded_at DESC";
$filesQuery = mysqli_query($conn, $filesSQL);

// ดึงข้อมูลรายงานทั้งหมดจากตาราง reports พร้อม JOIN กับ users
$reportsSQL = "SELECT reports.*, users.username 
               FROM reports 
               JOIN users ON reports.user_id = users.id 
               ORDER BY created_at DESC";
$reportsQuery = mysqli_query($conn, $reportsSQL);
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>หน้าแรก (Home)</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
</head>

<body>
  <?php include 'navbar.php'; ?> <!-- เรียกใช้ Navbar -->

  <div class="container my-4">
    <h2>หน้าแรก (Home)</h2>

    <!-- ลิงก์นำทาง -->
    <div class="mb-4">
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="upload.php" class="btn btn-primary">อัปโหลดไฟล์</a>
        <a href="show_files.php" class="btn btn-warning">ดูไฟล์ (Filter)</a>
        <a href="send_report.php" class="btn btn-info">ส่งรายงาน</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-success">เข้าสู่ระบบ</a>
        <a href="register.php" class="btn btn-secondary">สมัครสมาชิก</a>
      <?php endif; ?>
    </div>

    <!-- ตารางแสดงไฟล์ทั้งหมด -->
    <h4>ไฟล์ทั้งหมด</h4>
    <table class="table table-bordered mb-5">
      <thead>
        <tr>
          <th>ชื่อไฟล์</th>
          <th>โดย</th>
          <th>เปิด/ดาวน์โหลด</th>
          <th>อัปโหลดเมื่อ</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($file = mysqli_fetch_assoc($filesQuery)): ?>
          <tr>
            <td><?php echo $file['file_name']; ?></td>
            <td><?php echo $file['username']; ?></td>
            <td>
              <!-- ปุ่มเปิดไฟล์ -->
              <a href="<?php echo $file['file_path']; ?>" target="_blank" class="btn btn-sm btn-primary me-1">
                Open
              </a>
              <!-- ปุ่มดาวน์โหลดไฟล์ -->
              <a href="<?php echo $file['file_path']; ?>" download class="btn btn-sm btn-success">
                Download
              </a>
            </td>
            <td><?php echo $file['uploaded_at']; ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <!-- ตารางแสดงรายละเอียดรายงาน พร้อมไฟล์แนบ -->
    <h4>รายละเอียดรายงาน</h4>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>หัวข้อรายงาน</th>
          <th>รายละเอียด</th>
          <th>โดย</th>
          <th>ส่งเมื่อ</th>
          <th>ไฟล์แนบ</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($report = mysqli_fetch_assoc($reportsQuery)): ?>
          <tr>
            <td><?php echo $report['subject']; ?></td>
            <td><?php echo $report['details']; ?></td>
            <td><?php echo $report['username']; ?></td>
            <td><?php echo $report['created_at']; ?></td>
            <td>
              <?php
              // ดึงไฟล์แนบสำหรับรายงานนี้
              $attachmentsSQL = "SELECT uploads.file_name, uploads.file_path 
                                 FROM report_attachments 
                                 JOIN uploads ON report_attachments.upload_id = uploads.id 
                                 WHERE report_attachments.report_id = '" . $report['id'] . "'";
              $attachmentsResult = mysqli_query($conn, $attachmentsSQL);
              if (mysqli_num_rows($attachmentsResult) > 0):
                while ($attach = mysqli_fetch_assoc($attachmentsResult)):
              ?>
                  <div>
                    <a href="<?php echo $attach['file_path']; ?>" target="_blank">
                      <?php echo $attach['file_name']; ?>
                    </a>
                  </div>
              <?php
                endwhile;
              else:
                echo "ไม่มีไฟล์แนบ";
              endif;
              ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>