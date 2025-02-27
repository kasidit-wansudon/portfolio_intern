<?php
// manage_admin.php
include 'config.php';
session_start();

// ถ้าไม่ล็อกอิน ให้เด้งไป login
if(!isset($_SESSION['user_id'])){
  header('Location: login.php');
  exit;
}

// ----------------------------------------
// 1) รับโหมดการทำงาน: category / file
// ----------------------------------------
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'category';

// ----------------------------------------
// 2) ค้นหา (search) ตามโหมด
// ----------------------------------------
$search = isset($_GET['search']) ? $_GET['search'] : '';

// ----------------------------------------
// 3) ลบ Category (ต้องไม่มีไฟล์เชื่อม)
// ----------------------------------------
if(isset($_GET['del_cat_id'])){
  $del_cat_id = $_GET['del_cat_id'];
  // เช็คว่าหมวดนี้มีไฟล์อยู่หรือไม่
  $checkFileSQL = "SELECT id FROM uploads WHERE category_id='$del_cat_id' LIMIT 1";
  $checkFileRes = mysqli_query($conn, $checkFileSQL);
  if(mysqli_num_rows($checkFileRes) > 0){
    // มีไฟล์อยู่ => ห้ามลบ
    $_SESSION['msg'] = "cannot_delete"; // ไว้ยิง sweet alert
  } else {
    // ลบได้
    $delSQL = "DELETE FROM categories WHERE id='$del_cat_id'";
    mysqli_query($conn, $delSQL);
    $_SESSION['msg'] = "delete_ok";
  }
  // รีเฟรชหน้าเพื่อเคลียร์ param
  header("Location: manage_admin.php?mode=category");
  exit;
}

// ----------------------------------------
// 4) ลบ File
// ----------------------------------------
if(isset($_GET['del_file_id'])){
  $del_file_id = $_GET['del_file_id'];
  // (ถ้าต้องลบไฟล์จริงในโฟลเดอร์ uploads ด้วย ก็ต้องไป unlink() เพิ่ม)
  // ตัวอย่าง: 
  // $fileRow = mysqli_fetch_assoc(mysqli_query($conn,"SELECT file_path FROM uploads WHERE id='$del_file_id'"));
  // if($fileRow){ unlink($fileRow['file_path']); }

  $delSQL = "DELETE FROM uploads WHERE id='$del_file_id'";
  mysqli_query($conn, $delSQL);
  $_SESSION['msg'] = "delete_ok";
  header("Location: manage_admin.php?mode=file");
  exit;
}

// ----------------------------------------
// 5) แก้ไข Category
// ----------------------------------------
if(isset($_POST['edit_category'])){
  $cat_id   = $_POST['cat_id'];
  $cat_name = $_POST['cat_name'];

  // อัปเดตชื่อใน DB
  $updSQL = "UPDATE categories SET category_name='$cat_name' WHERE id='$cat_id'";
  mysqli_query($conn, $updSQL);

  $_SESSION['msg'] = "update_ok";
  header("Location: manage_admin.php?mode=category");
  exit;
}

// ----------------------------------------
// 6) แก้ไข File (เฉพาะเปลี่ยนชื่อไฟล์สมมติ)
// ----------------------------------------
if(isset($_POST['edit_file'])){
  $file_id   = $_POST['file_id'];
  $file_name = $_POST['file_name'];
  // อัปเดต DB (เปลี่ยนชื่อไฟล์เฉพาะใน DB; ถ้าจะเปลี่ยนจริง ต้อง rename ในโฟลเดอร์ด้วย)
  $updSQL = "UPDATE uploads SET file_name='$file_name' WHERE id='$file_id'";
  mysqli_query($conn, $updSQL);

  $_SESSION['msg'] = "update_ok";
  header("Location: manage_admin.php?mode=file");
  exit;
}

// ----------------------------------------
// 7) คำสั่ง SELECT สำหรับแต่ละโหมด
// ----------------------------------------
if($mode == 'category'){
  // ค้นหาหมวดหมู่
  if($search != ''){
    $catSQL = "SELECT * FROM categories 
               WHERE category_name LIKE '%$search%' 
               ORDER BY id DESC";
  } else {
    $catSQL = "SELECT * FROM categories ORDER BY id DESC";
  }
  $catRes = mysqli_query($conn, $catSQL);
}
else if($mode == 'file'){
  // ค้นหาไฟล์
  if($search != ''){
    $fileSQL = "SELECT * FROM uploads 
                WHERE file_name LIKE '%$search%' 
                ORDER BY id DESC";
  } else {
    $fileSQL = "SELECT * FROM uploads ORDER BY id DESC";
  }
  $fileRes = mysqli_query($conn, $fileSQL);
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
  <!-- SweetAlert2 -->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include 'navbar.php'; ?> <!-- ถ้ามี Navbar -->

<div class="container mt-4">
  <h2>จัดการระบบ (Admin)</h2>

  <!-- แสดงปุ่มสลับโหมด -->
  <div class="mb-3">
    <a href="?mode=category" class="btn btn-outline-primary 
       <?php echo ($mode=='category'?'active':''); ?>">
      จัดการหมวดหมู่
    </a>
    <a href="?mode=file" class="btn btn-outline-success
       <?php echo ($mode=='file'?'active':''); ?>">
      จัดการไฟล์
    </a>
  </div>

  <!-- ฟอร์มค้นหา -->
  <form method="get" class="mb-3">
    <!-- ส่ง mode กลับไปด้วย -->
    <input type="hidden" name="mode" value="<?php echo $mode; ?>">
    <div class="input-group" style="max-width: 400px;">
      <input type="text" name="search" class="form-control" 
             placeholder="ค้นหา..." 
             value="<?php echo $search; ?>">
      <button type="submit" class="btn btn-secondary">ค้นหา</button>
    </div>
  </form>

  <?php if($mode=='category'): ?>

    <!-- =========================
         ส่วนจัดการหมวดหมู่
         ========================= -->
    <h4>ตารางหมวดหมู่</h4>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>ชื่อหมวดหมู่</th>
          <th>จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php while($cat = mysqli_fetch_assoc($catRes)): ?>
        <tr>
          <td><?php echo $cat['id']; ?></td>
          <td><?php echo $cat['category_name']; ?></td>
          <td>
            <!-- ปุ่มแก้ไข -->
            <button class="btn btn-sm btn-warning"
              onclick="editCategory(
                '<?php echo $cat['id']; ?>',
                '<?php echo $cat['category_name']; ?>'
              )">
              แก้ไข
            </button>
            <!-- ปุ่มลบ (ใช้ SweetAlert Confirm) -->
            <button class="btn btn-sm btn-danger"
              onclick="confirmDeleteCategory('<?php echo $cat['id']; ?>')">
              ลบ
            </button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

  <?php else: ?>

    <!-- =========================
         ส่วนจัดการไฟล์
         ========================= -->
    <h4>ตารางไฟล์</h4>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>ชื่อไฟล์</th>
          <th>Path</th>
          <th>อัปโหลดเมื่อ</th>
          <th>จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php while($file = mysqli_fetch_assoc($fileRes)): ?>
        <tr>
          <td><?php echo $file['id']; ?></td>
          <td><?php echo $file['file_name']; ?></td>
          <td><?php echo $file['file_path']; ?></td>
          <td><?php echo $file['uploaded_at']; ?></td>
          <td>
            <!-- ปุ่มแก้ไขไฟล์ -->
            <button class="btn btn-sm btn-warning"
              onclick="editFile(
                '<?php echo $file['id']; ?>',
                '<?php echo $file['file_name']; ?>'
              )">
              แก้ไข
            </button>
            <!-- ปุ่มลบไฟล์ -->
            <button class="btn btn-sm btn-danger"
              onclick="confirmDeleteFile('<?php echo $file['id']; ?>')">
              ลบ
            </button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

  <?php endif; ?>
</div>

<!-- ===============================
     ฟอร์มซ่อนสำหรับ edit Category
     =============================== -->
<form method="post" id="editCategoryForm">
  <input type="hidden" name="cat_id" id="cat_id">
  <input type="hidden" name="cat_name" id="cat_name">
  <input type="hidden" name="edit_category" value="1">
</form>

<!-- ===========================
     ฟอร์มซ่อนสำหรับ edit File
     =========================== -->
<form method="post" id="editFileForm">
  <input type="hidden" name="file_id" id="file_id">
  <input type="hidden" name="file_name" id="file_name">
  <input type="hidden" name="edit_file" value="1">
</form>

<script>
// ===========================================
// SweetAlert แสดงข้อความ (ถ้ามีใน SESSION)
// ===========================================
<?php if(isset($_SESSION['msg'])): ?>
  let msg = '<?php echo $_SESSION['msg']; ?>';
  <?php unset($_SESSION['msg']); ?>
  if(msg === 'cannot_delete'){
    Swal.fire({
      icon: 'error',
      title: 'ลบไม่ได้',
      text: 'หมวดหมู่นี้ยังมีไฟล์อยู่ ไม่สามารถลบได้'
    });
  }
  else if(msg === 'delete_ok'){
    Swal.fire({
      icon: 'success',
      title: 'ลบเรียบร้อย!',
      showConfirmButton: false,
      timer: 1500
    });
  }
  else if(msg === 'update_ok'){
    Swal.fire({
      icon: 'success',
      title: 'บันทึกการแก้ไขสำเร็จ!',
      showConfirmButton: false,
      timer: 1500
    });
  }
<?php endif; ?>

// ===========================================
// ฟังก์ชัน Confirm ลบ Category
// ===========================================
function confirmDeleteCategory(catId){
  Swal.fire({
    title: 'ยืนยันการลบหมวดหมู่?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#999',
    confirmButtonText: 'ลบ',
    cancelButtonText: 'ยกเลิก'
  }).then((result) => {
    if(result.isConfirmed){
      // ถ้ากดตกลง => ไปยังลิงก์ลบ
      window.location = "manage_admin.php?mode=category&del_cat_id=" + catId;
    }
  });
}

// ===========================================
// ฟังก์ชัน Confirm ลบ File
// ===========================================
function confirmDeleteFile(fileId){
  Swal.fire({
    title: 'ยืนยันการลบไฟล์?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#999',
    confirmButtonText: 'ลบ',
    cancelButtonText: 'ยกเลิก'
  }).then((result) => {
    if(result.isConfirmed){
      window.location = "manage_admin.php?mode=file&del_file_id=" + fileId;
    }
  });
}

// ===========================================
// ฟังก์ชัน Edit Category (Prompt รับชื่อใหม่)
// ===========================================
function editCategory(catId, catName){
  Swal.fire({
    title: 'แก้ไขหมวดหมู่',
    input: 'text',
    inputValue: catName,
    showCancelButton: true,
    confirmButtonText: 'บันทึก',
    cancelButtonText: 'ยกเลิก'
  }).then((result) => {
    if(result.isConfirmed){
      // ใส่ค่าในฟอร์มซ่อนแล้ว submit
      document.getElementById('cat_id').value = catId;
      document.getElementById('cat_name').value = result.value;
      document.getElementById('editCategoryForm').submit();
    }
  });
}

// ===========================================
// ฟังก์ชัน Edit File (Prompt รับชื่อไฟล์ใหม่)
// ===========================================
function editFile(fileId, fileName){
  Swal.fire({
    title: 'แก้ไขชื่อไฟล์',
    input: 'text',
    inputValue: fileName,
    showCancelButton: true,
    confirmButtonText: 'บันทึก',
    cancelButtonText: 'ยกเลิก'
  }).then((result) => {
    if(result.isConfirmed){
      document.getElementById('file_id').value = fileId;
      document.getElementById('file_name').value = result.value;
      document.getElementById('editFileForm').submit();
    }
  });
}
</script>
</body>
</html>
