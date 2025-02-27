<?php
// connect DB
include 'config.php';
session_start();

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql    = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    $row    = mysqli_fetch_assoc($result);

    if($row && password_verify($password, $row['password'])){
        $_SESSION['user_id'] = $row['id'];
        // เก็บ username ด้วย เพื่อเอาไปโชว์บน Navbar ได้ง่าย
        $_SESSION['username'] = $row['username'];
        
        header('Location: upload.php');
    } else {
        $error = "❌ Username หรือ Password ไม่ถูกต้อง";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">

  <!-- เพิ่มสไตล์เล็กน้อย -->
  <style>
    body {
      min-height: 100vh;
      display: flex; 
      justify-content: center; 
      align-items: center;
      background: linear-gradient(135deg, #FEB692, #EA5455);
      font-family: "Prompt", sans-serif;
    }
    .login-card {
      background: #ffffffcc;
      backdrop-filter: blur(10px);
      border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.2);
      padding: 2rem;
      width: 100%;
      max-width: 400px;
    }
    .btn-custom {
      background: #EA5455;
      border: none;
    }
    .btn-custom:hover {
      background: #f55e5f;
    }
  </style>
</head>
<body>

<div class="login-card">
  <h3 class="text-center mb-4">เข้าสู่ระบบ 🚀</h3>
  <?php if(!empty($error)): ?>
    <div class="alert alert-danger">
      <?php echo $error; ?>
    </div>
  <?php endif; ?>

  <form method="post" action="">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" name="login" class="btn btn-custom text-white w-100">Login</button>
  </form>

  <hr>
  <div class="text-center">
    <span>ยังไม่มีบัญชีผู้ใช้ใช่ไหม?</span>
    <a href="register.php" class="text-decoration-none fw-bold">สมัครสมาชิก</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
