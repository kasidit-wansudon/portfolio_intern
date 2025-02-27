<?php
include 'config.php';
session_start();

if(isset($_POST['register'])){
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // บันทึกลง DB
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    mysqli_query($conn, $sql);

    // สมัครเสร็จ ส่งไปหน้า login
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>สมัครสมาชิก</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
  <style>
    /* พื้นหลังเป็น Gradient สวย ๆ */
    body {
      background: linear-gradient(to bottom right, #78ffd6, #007991);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Prompt', sans-serif;
    }
    .card {
      border: none;
      border-radius: 1rem;
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
      overflow: hidden;
    }
    .card-header {
      background: #fff;
      border-bottom: none;
      text-align: center;
      font-weight: 600;
      font-size: 1.5rem;
    }
    .card-body {
      background: #f8f9fa;
      padding: 2rem;
    }
    .form-control,
    .btn {
      border-radius: 30px;
    }
    .btn-primary {
      background: #007991;
      border: none;
      transition: all 0.3s;
    }
    .btn-primary:hover {
      background: #005f6f;
    }
    .footer-text {
      text-align: center;
      margin-top: 1rem;
    }
    .footer-text a {
      text-decoration: none;
      color: #007991;
      font-weight: 500;
    }
    .footer-text a:hover {
      color: #005f6f;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card">
        <div class="card-header">
        สมัครสมาชิก
        </div>
        <div class="card-body">
          <form method="post">
            <div class="mb-3">
              <label>Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="d-grid">
              <button type="submit" name="register" class="btn btn-primary btn-block">
                ✨ สมัครสมาชิก ✨
              </button>
            </div>
          </form>
          <div class="footer-text">
            มีบัญชีแล้ว? 
            <a href="login.php">เข้าสู่ระบบที่นี่</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
