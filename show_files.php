<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// ดึงหมวดหมู่ทั้งหมด
$catSQL   = "SELECT * FROM categories";
$catQuery = mysqli_query($conn, $catSQL);

// กดเลือก Filter
$whereCondition = "";
if (isset($_GET['category_id']) && $_GET['category_id'] != '') {
  $catId = $_GET['category_id'];
  $whereCondition = "WHERE category_id = '$catId'";
}

// ดึงข้อมูลไฟล์ทั้งหมดจาก uploads
$filesSQL = "SELECT uploads.*, users.username 
             FROM uploads 
             JOIN users ON uploads.user_id = users.id 
             $whereCondition
             ORDER BY uploaded_at DESC";
$filesQuery = mysqli_query($conn, $filesSQL);

// สร้าง array เก็บไฟล์ทั้งหมด (จะได้วนลูปได้ทั้ง List และ Grid)
$files = [];
while ($f = mysqli_fetch_assoc($filesQuery)) {
  $files[] = $f;
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>All Files</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
</head>

<body>

  <?php include 'navbar.php'; ?> <!-- เรียกใช้ Navbar -->

  <div class="container mt-4">
    <h2>ไฟล์ทั้งหมด</h2>

    <!-- ฟอร์ม Filter หมวดหมู่ -->
    <form method="get" action="" class="mb-3">
      <div class="row g-2">
        <div class="col-auto">
          <select name="category_id" class="form-select">
            <option value="">-- ทุกหมวดหมู่ --</option>
            <?php
            // วนลูปใหม่ ต้อง reset pointer หรือ query แยก
            // (กรณีนี้จะ simplify โดยดึง categories ก่อนแล้วค่อย while)
            mysqli_data_seek($catQuery, 0); // รีเซ็ต pointer
            while ($cat = mysqli_fetch_assoc($catQuery)): ?>
              <option value="<?php echo $cat['id']; ?>">
                <?php echo $cat['category_name']; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-secondary">Filter</button>
        </div>

        <!-- ปุ่มสลับ List / Grid -->
        <div class="col-auto">
          <button type="button" class="btn btn-outline-primary" onclick="showListView()">List View</button>
          <button type="button" class="btn btn-outline-success" onclick="showGridView()">Grid View</button>
        </div>
      </div>
    </form>

    <!-- =========================
       ส่วน List View (Table)
       ========================= -->
    <div id="listView" style="display: none;"><!-- เริ่มต้นซ่อน ไว้เดี๋ยวใช้ JS ควบคุม -->
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ชื่อไฟล์</th>
            <th>พรีวิว/ดาวน์โหลด</th>
            <th>อัปโหลดเมื่อ</th>
            <th>Preview (Modal)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($files as $file): ?>
            <?php
            $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
            ?>
            <tr>
              <td><?php echo $file['file_name']; ?></td>
              <td>
                <a href="<?php echo $file['file_path']; ?>" target="_blank">
                  Open/Download
                </a>
              </td>
              <td><?php echo $file['uploaded_at']; ?></td>
              <td>
                <!-- ปุ่ม Preview เปิด Modal (ส่ง data-attr) -->
                <button type="button"
                  class="btn btn-info"
                  data-bs-toggle="modal"
                  data-bs-target="#previewModal"
                  data-filepath="<?php echo $file['file_path']; ?>"
                  data-ext="<?php echo $ext; ?>"
                  data-filename="<?php echo $file['file_name']; ?>">
                  Preview
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- =========================
     ส่วน Grid View (Cards)
     ========================= -->
    <div id="gridView" class="row" style="display: none;">
      <?php foreach ($files as $file): ?>
        <?php
        $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
        $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm']); // เพิ่มเช็คไฟล์วิดีโอ
        ?>
        <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
          <div class="card">
            <?php if ($isImage): ?>
              <!-- ไฟล์รูป -->
              <img src="<?php echo $file['file_path']; ?>"
                class="card-img-top"
                alt="preview"
                style="height: 200px; object-fit: cover;">

            <?php elseif ($isVideo): ?>
              <!-- ไฟล์วิดีโอ -->
              <video controls
                style="width: 100%; height: 200px; object-fit: cover;">
                <source src="<?php echo $file['file_path']; ?>" type="video/<?php echo $ext; ?>">
                Browser ไม่รองรับการเล่นวิดีโอ
              </video>

            <?php else: ?>
              <!-- ไม่รองรับการพรีวิว -->
              <div class="d-flex align-items-center justify-content-center"
                style="height: 200px; background-color: #f7f7f7;">
                <p class="text-danger mb-0">ไม่รองรับการพรีวิว</p>
              </div>
            <?php endif; ?>

            <div class="card-body">
              <!-- ชื่อไฟล์ -->
              <h6 class="card-title"
                style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <?php echo $file['file_name']; ?>
              </h6>
              <p class="card-text mb-1">
                อัปโหลดเมื่อ: <?php echo $file['uploaded_at']; ?>
              </p>
              <p class="card-text text-muted" style="font-size: 0.875rem;">
                โดย: <?php echo $file['username']; ?>
              </p>

              <a href="<?php echo $file['file_path']; ?>"
                target="_blank"
                class="btn btn-sm btn-primary">
                Open/Download
              </a>
              <!-- ถ้าต้องการปุ่ม Preview Modal สำหรับวิดีโอเหมือนรูป ก็เพิ่มได้ตามเดิม -->
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>


  </div>

  <!-- ===================================
     Modal สำหรับ Preview ไฟล์
     =================================== -->
  <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- modal-xl = กว้างพิเศษ -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="previewTitle">Preview</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="previewBody">
          <!-- เนื้อหาไฟล์จะแทรกด้วย JavaScript -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS (ต้องมีเพื่อให้ Modal ใช้งานได้) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // === เปลี่ยนโหมดการแสดงผลด้วย JS + localStorage ===
    function showListView() {
      document.getElementById('listView').style.display = 'block';
      document.getElementById('gridView').style.display = 'none';
      localStorage.setItem('viewMode', 'list');
    }

    function showGridView() {
      document.getElementById('listView').style.display = 'none';
      document.getElementById('gridView').style.display = 'flex';
      localStorage.setItem('viewMode', 'grid');
    }

    // เปิดหน้ามา โหลดโหมดจาก localStorage
    document.addEventListener("DOMContentLoaded", function() {
      let savedView = localStorage.getItem('viewMode');
      if (!savedView || savedView === 'list') {
        showListView();
      } else {
        showGridView();
      }
    });

    // === Modal Preview ไฟล์ ===
    var previewModal = document.getElementById('previewModal');
    previewModal.addEventListener('show.bs.modal', function(event) {
      var button = event.relatedTarget; // ปุ่มที่ถูกกด
      var filePath = button.getAttribute('data-filepath');
      var fileExt = button.getAttribute('data-ext');
      var fileName = button.getAttribute('data-filename');

      // เปลี่ยน title
      var previewTitle = previewModal.querySelector('#previewTitle');
      previewTitle.textContent = "Preview: " + fileName;

      // เตรียมพื้นที่ modal body
      var previewBody = previewModal.querySelector('#previewBody');
      previewBody.innerHTML = ""; // เคลียร์ก่อนทุกครั้ง

      // เช็คประเภทไฟล์จาก extension
      if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
        // ไฟล์รูป
        previewBody.innerHTML = `<img src="${filePath}" alt="${fileName}" class="img-fluid">`;
      } else if (['mp4', 'mov', 'avi', 'webm'].includes(fileExt)) {
        // ไฟล์วิดีโอ
        previewBody.innerHTML = `
      <video controls autoplay style="max-width: 100%;">
        <source src="${filePath}" type="video/${fileExt}">
        Browser ของคุณไม่รองรับการเล่นวิดีโอ
      </video>
    `;
      } else if (fileExt === 'pdf') {
        // ไฟล์ PDF
        previewBody.innerHTML = `
      <iframe src="${filePath}" width="100%" height="600px"></iframe>
    `;
      } else {
        // อื่นๆ (ไม่รองรับพรีวิว) -> แสดงปุ่มดาวน์โหลดแทน
        previewBody.innerHTML = `
      <p>ไฟล์ประเภทนี้ไม่รองรับการพรีวิว</p>
      <a href="${filePath}" class="btn btn-primary" download>ดาวน์โหลดไฟล์</a>
    `;
      }
    });
  </script>

</body>

</html>