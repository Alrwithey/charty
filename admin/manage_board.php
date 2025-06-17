<?php
require_once 'includes/auth_check.php';
// check_permission('manage_board');
require_once '../config.php';
$member_type_value = 'board';
$page_title = 'إدارة أعضاء مجلس الإدارة';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die('CSRF token validation failed.'); }

    if (isset($_POST['add_member'])) {
        $name = $_POST['name'];
        $position = $_POST['position'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $term_duration = $_POST['term_duration'];
        $member_order = intval($_POST['member_order']);
        $photo_path = 'uploads/members/placeholder.png'; 

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $target_dir = "../uploads/members/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            $file_name = time() . '_' . basename($_FILES["photo"]["name"]);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo_path = "uploads/members/" . $file_name;
            }
        }
        $stmt = $conn->prepare("INSERT INTO board_members (name, position, photo, phone, email, term_duration, member_type, member_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", $name, $position, $photo_path, $phone, $email, $term_duration, $member_type_value, $member_order);
        $stmt->execute();
    }
    header("Location: manage_board.php?success=1");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // يمكنك إضافة كود لحذف الصورة من السيرفر هنا
    $stmt = $conn->prepare("DELETE FROM board_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_board.php?success=2");
    exit();
}

$members_result = $conn->query("SELECT * FROM board_members WHERE member_type = 'board' ORDER BY member_order ASC");
include 'includes/header.php';
?>

<h1><?php echo $page_title; ?></h1>
<?php if(isset($_GET['success'])): ?><p class="success">تم تنفيذ العملية بنجاح.</p><?php endif; ?>
<div class="form-container">
    <h2>إضافة عضو جديد لمجلس الإدارة</h2>
    <form action="manage_board.php" method="POST" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group"><label>الاسم الكامل</label><input type="text" name="name" required></div>
        <div class="form-group"><label>المنصب</label><input type="text" name="position" required></div>
        <div class="form-group"><label>الصورة الشخصية (اختياري)</label><input type="file" name="photo"></div>
        <div class="form-group"><label>رقم الجوال (اختياري)</label><input type="text" name="phone"></div>
        <div class="form-group"><label>البريد الإلكتروني (اختياري)</label><input type="email" name="email"></div>
        <div class="form-group"><label>المدة المتاحة في المجلس (مثال: 4 سنوات)</label><input type="text" name="term_duration"></div>
        <div class="form-group"><label>ترتيب العرض</label><input type="number" name="member_order" value="0" required></div>
        <button type="submit" name="add_member">إضافة عضو</button>
    </form>
</div>
<div class="table-container">
    <h2>الأعضاء الحاليون</h2>
    <table>
        <thead><tr><th>الصورة</th><th>الاسم</th><th>المنصب</th><th>إجراءات</th></tr></thead>
        <tbody>
            <?php if($members_result && $members_result->num_rows > 0): ?>
                <?php while($member = $members_result->fetch_assoc()): ?>
                <tr>
                    <td><img src="../<?php echo e($member['photo']); ?>" alt="" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;"></td>
                    <td><?php echo e($member['name']); ?></td>
                    <td><?php echo e($member['position']); ?></td>
                    <td>
                        <a href="edit_member.php?id=<?php echo $member['id']; ?>" class="btn-edit">تعديل</a>
                        <a href="manage_board.php?delete=<?php echo $member['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">حذف</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                 <tr><td colspan="4" style="text-align:center;">لم تتم إضافة أي أعضاء بعد.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include 'includes/footer.php'; ?>