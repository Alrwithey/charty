<?php
require_once 'includes/auth_check.php';
require_once '../config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) { header("Location: dashboard.php"); exit(); }

$stmt = $conn->prepare("SELECT * FROM board_members WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
if (!$member) { header("Location: dashboard.php"); exit(); }

$redirect_page = ($member['member_type'] == 'board') ? 'manage_board.php' : 'manage_assembly.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die('CSRF token validation failed.'); }
    
    $name = $_POST['name'];
    $position = $_POST['position'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $term_duration = $_POST['term_duration'];
    $member_order = intval($_POST['member_order']);
    $photo_path = $_POST['current_photo'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "../uploads/members/";
        $file_name = time() . '_' . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            if (!empty($photo_path) && $photo_path != 'uploads/members/placeholder.png' && file_exists("../" . $photo_path)) {
                @unlink("../" . $photo_path);
            }
            $photo_path = "uploads/members/" . $file_name;
        }
    }

    $stmt_update = $conn->prepare("UPDATE board_members SET name=?, position=?, photo=?, phone=?, email=?, term_duration=?, member_order=? WHERE id=?");
    $stmt_update->bind_param("ssssssii", $name, $position, $photo_path, $phone, $email, $term_duration, $member_order, $id);
    $stmt_update->execute();
    header("Location: " . $redirect_page . "?success=1");
    exit();
}

include 'includes/header.php';
?>
<h1>تعديل بيانات العضو: <?php echo e($member['name']); ?></h1>
<div class="form-container">
    <form action="edit_member.php?id=<?php echo $id; ?>" method="POST" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group"><label>الاسم الكامل</label><input type="text" name="name" value="<?php echo e($member['name']); ?>" required></div>
        <div class="form-group"><label>المنصب/الصفة</label><input type="text" name="position" value="<?php echo e($member['position']); ?>" required></div>
        <div class="form-group"><label>الصورة الحالية</label><br><img src="../<?php echo e($member['photo']); ?>" alt="<?php echo e($member['name']); ?>" style="width:100px; height:100px; border-radius:50%; object-fit:cover;"></div>
        <div class="form-group"><label>تغيير الصورة</label><input type="file" name="photo"><input type="hidden" name="current_photo" value="<?php echo e($member['photo']); ?>"></div>
        <div class="form-group"><label>رقم الجوال</label><input type="text" name="phone" value="<?php echo e($member['phone']); ?>"></div>
        <div class="form-group"><label>البريد الإلكتروني</label><input type="email" name="email" value="<?php echo e($member['email']); ?>"></div>
        <div class="form-group"><label>المدة المتاحة</label><input type="text" name="term_duration" value="<?php echo e($member['term_duration']); ?>"></div>
        <div class="form-group"><label>ترتيب العرض</label><input type="number" name="member_order" value="<?php echo e($member['member_order']); ?>" required></div>
        <button type="submit">حفظ التعديلات</button>
        <a href="<?php echo $redirect_page; ?>" style="margin-right:15px;">إلغاء</a>
    </form>
</div>
<?php include 'includes/footer.php'; ?>