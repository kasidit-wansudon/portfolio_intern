<?php
session_start();
include './config.php';

// 1. ถ้า staff ยังไม่ login → redirect ไป login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}

// 2. ดึงข้อมูลจาก URL
$code   = $_GET['code'] ?? '';
$type   = $_GET['type'] ?? 'checkin';
$userId = $_GET['user'] ?? 0;
$staffId = $_SESSION['user_id'];
$now    = date('Y-m-d H:i:s');

// 3. บันทึกข้อมูลลงตาราง attendance
$stmt = $conn->prepare("INSERT INTO attendance (user_id, staff_id, type, time, code) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $userId, $staffId, $type, $now, $code);
$stmt->execute();

// 4. บันทึกข้อความแจ้งเตือน แล้ว redirect
$_SESSION['message'] = "✅ " . ucfirst($type) . " สำเร็จเวลา $now";
header("Location: history.php");
exit;
?>
