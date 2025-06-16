<?php
require_once 'includes/auth_check.php';
check_permission('manage_regulations'); // التحقق من الصلاحية
require_once '../config.php';

// التعامل مع الطلبات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    if (isset($_POST['add_regulation'])) {
        $title = $_POST['title'];
        $order = $_POST['regulation_order'];
        $file_path = '';

        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $target_dir = "../uploads/pdf/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            // استخدام اسم آمن للملف
            $file_name = time() . '_' . preg_replace('/[^A-Za-z0-9.\-]/', '_', basename($_FILES["file"]["name"]));
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $file_path = "uploads/pdf/" . $file_name;
            }
        }
        if(!empty($file_path)){
            $stmt = $conn->prepare("INSERT INTO regulations (title, file_path, regulation_order) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $title, $file_path, $order);
            $stmt->execute();
        }
    }
    header("Location: manage_regulations.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // يمكنك إضافة كود لحذف الملف من السيرفر هنا
    $stmt = $conn->prepare("DELETE FROM regulations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_regulations.php");
    exit();
}

$regulations_result = $conn->query("SELECT * FROM regulations ORDER BY regulation_order ASC");

include 'includes/header.php';
?>

<h1>إدارة اللوائح والسياسات</h1>

<div class="form-container">
    <h2>إضافة لائحة/سياسة جديدة</h2>
    <form action="manage_regulations.php" method="POST" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group">
            <label>اسم اللائحة</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label>ملف PDF</label>
            <input type="file" name="file" accept=".pdf" required>
        </div>
        <div class="form-group">
            <label>ترتيب العرض</label>
            <input type="number" name="regulation_order" value="0" required>
        </div>
        <button type="submit" name="add_regulation">إضافة</button>
    </form>
</div>

<div class="table-container">
    <h2>اللوائح الحالية</h2>
    <table>
        <thead>
            <tr>
                <th>الاسم</th>
                <th>ترتيب العرض</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php while($regulation = $regulations_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo e($regulation['title']); ?></td>
                <td><?php echo e($regulation['regulation_order']); ?></td>
                <td>
                    <a href="../<?php echo e($regulation['file_path']); ?>" target="_blank" class="btn-edit">عرض الملف</a>
                    <a href="manage_regulations.php?delete=<?php echo $regulation['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>