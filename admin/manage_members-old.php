<?php
require_once 'includes/auth_check.php';
// check_permission('manage_members'); // يمكنك تفعيل هذا لاحقاً
require_once '../config.php';

// التعامل مع الطلبات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die('CSRF token validation failed.'); }

    $name = $_POST['name'];
    $position = $_POST['position'];
    $member_type = $_POST['member_type'];
    $member_order = intval($_POST['member_order']);
    
    // إضافة عضو جديد
    if (isset($_POST['add_member'])) {
        $photo_path = 'uploads/members/placeholder.png'; // مسار افتراضي
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $target_dir = "../uploads/members/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            $file_name = time() . '_' . basename($_FILES["photo"]["name"]);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo_path = "uploads/members/" . $file_name;
            }
        }
        $stmt = $conn->prepare("INSERT INTO board_members (name, position, photo, member_type, member_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $position, $photo_path, $member_type, $member_order);
        $stmt->execute();
    }
    header("Location: manage_members.php?success=1");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // يمكنك إضافة كود لحذف الصورة من السيرفر هنا
    $stmt = $conn->prepare("DELETE FROM board_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_members.php?success=2");
    exit();
}

$board_members = $conn->query("SELECT * FROM board_members WHERE member_type = 'board' ORDER BY member_order ASC");
$assembly_members = $conn->query("SELECT * FROM board_members WHERE member_type = 'assembly' ORDER BY member_order ASC");

include 'includes/header.php';
?>

<h1>إدارة أعضاء المجلس والجمعية العمومية</h1>
<?php if(isset($_GET['success'])): ?><p class="success">تم تنفيذ العملية بنجاح.</p><?php endif; ?>

<div class="form-container">
    <h2>إضافة عضو جديد</h2>
    <form action="manage_members.php" method="POST" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group"><label>الاسم الكامل</label><input type="text" name="name" required></div>
        <div class="form-group"><label>المنصب/الصفة</label><input type="text" name="position" required></div>
        <div class="form-group"><label>الصورة الشخصية (اختياري)</label><input type="file" name="photo"></div>
        <div class="form-group">
            <label>نوع العضوية</label>
            <select name="member_type" required>
                <option value="board">مجلس الإدارة</option>
                <option value="assembly">الجمعية العمومية</option>
            </select>
        </div>
        <div class="form-group"><label>ترتيب العرض</label><input type="number" name="member_order" value="0" required></div>
        <button type="submit" name="add_member">إضافة عضو</button>
    </form>
</div>

<div class="table-container">
    <h2>أعضاء مجلس الإدارة</h2>
    <table>
        <thead><tr><th>الصورة</th><th>الاسم</th><th>المنصب</th><th>إجراءات</th></tr></thead>
        <tbody>
            <?php while($member = $board_members->fetch_assoc()): ?>
            <tr>
                <td><img src="../<?php echo e($member['photo']); ?>" alt="" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;"></td>
                <td><?php echo e($member['name']); ?></td>
                <td><?php echo e($member['position']); ?></td>
                <td><a href="manage_members.php?delete=<?php echo $member['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">حذف</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="table-container">
    <h2>أعضاء الجمعية العمومية</h2>
    <table>
        <thead><tr><th>الصورة</th><th>الاسم</th><th>الصفة</th><th>إجراءات</th></tr></thead>
        <tbody>
            <?php while($member = $assembly_members->fetch_assoc()): ?>
            <tr>
                <td><img src="../<?php echo e($member['photo']); ?>" alt="" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;"></td>
                <td><?php echo e($member['name']); ?></td>
                <td><?php echo e($member['position']); ?></td>
                <td><a href="manage_members.php?delete=<?php echo $member['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">حذف</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>