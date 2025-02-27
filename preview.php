<?php
// preview.php
include 'config.php';
session_start();

// ถ้าต้องการบังคับให้เฉพาะคนล็อกอินดู
if(!isset($_SESSION['user_id'])){
  header('Location: login.php');
  exit;
}

// รับค่า id ของไฟล์จากพารามิเตอร์
if(!isset($_GET['id'])){
  die("ไม่พบไฟล์ที่ต้องการพรีวิว");
}
$fileId = $_GET['id'];

// ค้นหาไฟล์ในฐานข้อมูล
$sql     = "SELECT * FROM uploads WHERE id = '$fileId'";
$result  = mysqli_query($conn, $sql);
$fileRow = mysqli_fetch_assoc($result);

if(!$fileRow){
  die("ไม่พบไฟล์ในระบบ");
}

// ได้ path ของไฟล์
$filePath = $fileRow['file_path'];
$fileName = $fileRow['file_name'];

// ดึงนามสกุลไฟล์เพื่อนำไปแยกประเภท
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
?>
<!DOCTYPE html>
<html>
<head>
  <title>Preview - <?php echo $fileName; ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'navbar.php'; ?> <!-- เรียกใช้ Navbar ได้ถ้าต้องการ -->

<div class="container mt-4">
  <h2>Preview: <?php echo $fileName; ?></h2>
  
  <?php if(in_array($ext, ['jpg','jpeg','png','gif'])): ?>
    <!-- แสดงภาพ -->
    <img src="<?php echo $filePath; ?>" alt="image" class="img-fluid" />

  <?php elseif(in_array($ext, ['mp4','mov','avi','webm'])): ?>
    <!-- แสดงวิดีโอ -->
    <video controls width="720">
      <source src="<?php echo $filePath; ?>" type="video/<?php echo $ext; ?>">
      Browser ของคุณไม่รองรับการเล่นวิดีโอ
    </video>

  <?php elseif($ext === 'pdf'): ?>
    <!-- แสดง PDF (ผ่าน iframe หรือ embed ก็ได้) -->
    <iframe src="<?php echo $filePath; ?>" width="100%" height="600px"></iframe>

  <?php else: ?>
    <!-- ถ้าไม่ใช่ไฟล์ภาพ/วิดีโอ/PDF ก็ให้ดาวน์โหลดแทน -->
    <p>ไฟล์ประเภทนี้ไม่รองรับพรีวิวในเบราว์เซอร์</p>
    <a href="<?php echo $filePath; ?>" class="btn btn-primary" download>ดาวน์โหลดไฟล์</a>
  <?php endif; ?>

</div>
</body>
</html>
