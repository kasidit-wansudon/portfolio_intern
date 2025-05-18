<?php
// ตรวจสอบ login แล้ว
$_SESSION['staff_id'] = $staff_id;

$redirect = $_SESSION['redirect_after_login'] ?? 'history.php';
unset($_SESSION['redirect_after_login']);
header("Location: $redirect");
exit;
