<?php
require_once 'includes/auth_check.php';
check_permission('manage_partners'); // التحقق من الصلاحية
require_once '../config.php';

// التعامل مع الطلبات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die('CSRF token validation failed.'); }

    if (isset($_POST['add_partner'])) {
        $name = $_POST['name'];
        $order = $_POST['partner_order'];
        $logo_path = '';

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $target_dir = "../uploads/partners/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            $file_name = time() . '_' . basename($_FILES["logo"]["name"]);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $logo_path = "uploads/partners/" . $file_name;
            }
        }
        if(!empty($logo_path)){
            $stmt = $conn->prepare("INSERT INTO partners (name, logo, partner_order) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $name, $logo_path, $order);
            $stmt->execute();
        }
    }
    header("Location: manage_partners.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // يمكنك إضافة كود لحذف الصورة من السيرفر هنا
    $stmt = $conn->prepare("DELETE FROM partners WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_partners.php");
    exit();
}

$partners_result = $conn->query("SELECT * FROM partners ORDER BY partner_order ASC");

include 'includes/header.php';
?>

<h1>إدارة شركاء النجاح</h1>

<div class="form-container">
    <h2>إضافة شريك جديد</h2>
    <form action="manage_partners.php" method="POST" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group">
            <label>اسم الشريك</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>شعار الشريك (اللوجو)</label>
            <input type="file" name="logo" accept="image/png, image/jpeg, image/svg+xml" required>
        </div>
        <div class="form-group">
            <label>ترتيب العرض</label>
            <input type="number" name="partner_order" value="0" required>
        </div>
        <button type="submit" name="add_partner">إضافة</button>
    </form>
</div>

<div class="table-container">
    <h2>الشركاء الحاليون</h2>
    <table>
        <thead>
            <tr>
                <th>الشعار</th>
                <th>الاسم</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php while($partner = $partners_result->fetch_assoc()): ?>
            <tr>
                <td><img src="../<?php echo e($partner['logo']); ?>" alt="<?php echo e($partner['name']); ?>" style="height: 50px; background: #f1f1f1; padding: 5px; border-radius: 5px;"></td>
                <td><?php echo e($partner['name']); ?></td>
                <td>
                    <a href="manage_partners.php?delete=<?php echo $partner['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>