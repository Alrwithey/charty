<?php
require_once 'includes/auth_check.php';
check_permission('manage_projects'); // التحقق من الصلاحية
require_once '../config.php';

// التعامل مع الطلبات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $title = $_POST['title'];
    $content = $_POST['content'];

    // إضافة مشروع جديد
    if (isset($_POST['add_project'])) {
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/projects/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            $file_name = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = "uploads/projects/" . $file_name;
            }
        }
        if (!empty($image_path)) {
            $stmt = $conn->prepare("INSERT INTO projects (title, content, image) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $content, $image_path);
            $stmt->execute();
        }
    }
    header("Location: manage_projects.php?success=1");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // يمكنك إضافة كود لحذف الصورة من السيرفر هنا
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_projects.php?success=2");
    exit();
}

$projects_result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");

include 'includes/header.php';
?>

<h1>إدارة المشاريع</h1>
<?php if(isset($_GET['success']) && $_GET['success'] == 1): ?><p class="success">تم إضافة المشروع بنجاح.</p><?php endif; ?>
<?php if(isset($_GET['success']) && $_GET['success'] == 2): ?><p class="success">تم حذف المشروع بنجاح.</p><?php endif; ?>
<?php if(isset($_GET['success']) && $_GET['success'] == 3): ?><p class="success">تم تعديل المشروع بنجاح.</p><?php endif; ?>

<div class="form-container">
    <h2>إضافة مشروع جديد</h2>
    <form action="manage_projects.php" method="POST" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group">
            <label>عنوان المشروع</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label>تفاصيل المشروع (يمكن استخدام HTML)</label>
            <textarea name="content" rows="8" required></textarea>
        </div>
        <div class="form-group">
            <label>صورة المشروع</label>
            <input type="file" name="image" required>
        </div>
        <button type="submit" name="add_project">إضافة المشروع</button>
    </form>
</div>

<div class="table-container">
    <h2>المشاريع الحالية</h2>
    <table>
        <thead>
            <tr>
                <th>الصورة</th>
                <th>العنوان</th>
                <th>تاريخ الإضافة</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php while($project = $projects_result->fetch_assoc()): ?>
            <tr>
                <td><img src="../<?php echo e($project['image']); ?>" alt="" style="width: 100px; height: 60px; object-fit: cover; border-radius: 5px;"></td>
                <td><?php echo e($project['title']); ?></td>
                <td><?php echo date('Y-m-d', strtotime($project['created_at'])); ?></td>
                <td>
                    <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn-edit">تعديل</a>
                    <a href="manage_projects.php?delete=<?php echo $project['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>