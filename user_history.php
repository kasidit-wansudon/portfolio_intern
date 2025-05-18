<?php
session_start();
include './config.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT a.*, u.username AS staff_name
        FROM attendance a
        LEFT JOIN users u ON a.staff_id = u.id
        WHERE a.user_id = ?
        ORDER BY a.time DESC
        ";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  die("❌ SQL Prepare Error: " . $conn->error);
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>ประวัติการ Check-in/Checkout</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

  <?php include 'navbar.php'; ?>

  <div class="container py-5">
    <h2 class="mb-4 text-center">📋 ประวัติการ Check-in / Checkout</h2>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-primary text-center">
          <tr>
            <th>วันที่</th>
            <th>เวลา</th>
            <th>ประเภท</th>
            <th>พนักงานที่สแกน</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows === 0): ?>
            <tr>
              <td colspan="4" class="text-center text-muted">ยังไม่มีข้อมูล</td>
            </tr>
          <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= date('Y-m-d', strtotime($row['time'])) ?></td>
                <td><?= date('H:i:s', strtotime($row['time'])) ?></td>
                <td class="<?= $row['type'] === 'checkin' ? 'text-success' : 'text-danger' ?>">
                  <?= ucfirst($row['type']) ?>
                </td>
                <td><?= htmlspecialchars($row['staff_name'] ?? 'ไม่ทราบ') ?></td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>

</html>