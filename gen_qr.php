<?php
session_start();
include "./functions/function_gen_qr.php";
include "./config.php";

// ตรวจสอบว่า login แล้ว
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$userId = $_SESSION['user_id'];
$dateToday = date('Y-m-d');

// 1. ดึงข้อมูล checkin/checkout ของวันนี้
$sql = "SELECT * FROM attendance WHERE user_id = ? AND DATE(time) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $userId, $dateToday);
$stmt->execute();
$result = $stmt->get_result();

$hasCheckin = false;
$hasCheckout = false;

while ($row = $result->fetch_assoc()) {
  if ($row['type'] === 'checkin') $hasCheckin = true;
  if ($row['type'] === 'checkout') $hasCheckout = true;
}

// 2. ตัดสินใจว่าจะ gen หรือไม่
if ($hasCheckin && $hasCheckout) {
  $message = "✅ วันนี้คุณได้ Check-in และ Check-out เรียบร้อยแล้ว";
  $showQR = false;
} else {
  $type = $hasCheckin ? 'checkout' : 'checkin'; // ต่อจาก checkin → checkout
  $code = bin2hex(random_bytes(4));
  $time = time();
  $url = HOST . "/scan_qr.php?code=$code&type=$type&user=$userId";
  $image = generateQRCode($url);
  $showQR = true;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>สร้าง QR Code</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-lg border-0">
        <div class="card-body text-center">

          <?php if (!$showQR): ?>
            <h4 class="text-success mb-4"><?= $message ?></h4>
            <a href="index.php" class="btn btn-outline-secondary">⬅️ กลับหน้าหลัก</a>
          <?php else: ?>
            <div class="input-group mb-3">
              <input type="text" class="form-control text-center" id="qrLink" value="<?= htmlspecialchars($url) ?>" readonly>
              <button class="btn btn-outline-secondary" type="button" onclick="copyLink()">📋 คัดลอก</button>
            </div>

            <h3 class="card-title mb-4">🔐 QR สำหรับ <?= ucfirst($type) ?></h3>
            <img src="<?= $image ?>" alt="QR Code" class="img-fluid mb-4" style="max-width: 300px;">
            <p class="text-muted small">สแกน QR นี้เพื่อบันทึกเวลา <?= $type ?> ของพนักงาน</p>
            <p class="text-secondary small">สร้างเมื่อ: <?= date('Y-m-d H:i:s', $time ?? time()) ?></p>
            <a href="gen_qr.php" class="btn btn-outline-primary mt-3">🔄 สร้าง QR ใหม่</a>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
function copyLink() {
  const linkInput = document.getElementById("qrLink");
  linkInput.select();
  linkInput.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(linkInput.value).then(() => {
    // alert("คัดลอกแล้ว");
  }).catch(err => {
    alert("❌ ไม่สามารถคัดลอกได้");
  });
}
</script>

</body>
</html>
