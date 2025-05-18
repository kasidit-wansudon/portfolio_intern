<?php
session_start();

// ถ้ายังไม่ได้ login ให้กลับไป login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    // header("Location: login.php");
    exit;
}

// เก็บข้อความแล้วเคลียร์
$message = $_SESSION['message'] ?? 'ไม่มีข้อมูล';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ผลการทำรายการ</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #74ebd5, #ACB6E5);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      font-family: "Prompt", sans-serif;
    }
    .result-box {
      background: #fff;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      text-align: center;
    }
  </style>
</head>
<body>

<div class="result-box">
  <h2 class="mb-4">📋 สถานะรายการ</h2>
  <div class="alert alert-success fw-bold">
    <?= htmlspecialchars($message) ?>
  </div>
  <a href="gen_qr.php" class="btn btn-outline-primary mt-3">🔄 กลับไปสร้าง QR ใหม่</a>
</div>

</body>
</html>
