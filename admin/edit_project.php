<?php
require_once 'includes/auth_check.php';
check_permission('edit_project'); // التحقق من الصلاحية
require_once '../config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) {
    header("Location: manage_projects.php");
    exit();
}

// التعامل مع طلب التحديث
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $title = $_POST['title'];
    $content = $_POST['content'];
    $current_image = $_POST['current_image'];
    $image_path = $current_image;

    // التعامل مع رفع صورة جديدة
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/projects/";
        $file_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "uploads/projects/" . $file_name;
            if (!empty($current_image) && file_exists("../" . $current_image)) {
                @unlink("../" . $current_image);
            }
        }
    }

    $stmt = $conn->prepare("UPDATE projects SET title=?, content=?, image=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $content, $image_path, $id);
    $stmt->execute();
    header("Location: manage_projects.php?success=3");
    exit();
}

// جلب بيانات المشروع الحالي
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();

if (!$project) {
    header("Location: manage_projects.php");
    exit();
}

include 'includes/header.php';
?>

<h1>تعديل المشروع</h1>

<div class="form-container">
    <form action="edit_project.php?id=<?php echo $id; ?>" method="POST" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group">
            <label for="title">عنوان المشروع</label>
            <input type="text" name="title" id="title" value="<?php echo e($project['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="content">تفاصيل المشروع (يمكن استخدام HTML)</label>
            <textarea name="content" id="content" rows="10" required><?php echo e($project['content']); ?></textarea>
        </div>
        <div class="form-group">
            <label>الصورة الحالية</label>
            <img src="../<?php echo e($project['image']); ?>" alt="" style="max-width: 200px; border-radius: 5px;">
            <input type="hidden" name="current_image" value="<?php echo e($project['image']); ?>">
        </div>
        <div class="form-group">
            <label for="image">تغيير الصورة (اختياري)</label>
            <input type="file" name="image" id="image">
        </div>
        <button type="submit">حفظ التعديلات</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>