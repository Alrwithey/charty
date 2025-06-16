<?php
require_once 'includes/auth_check.php';
check_permission('edit_slider'); // التحقق من الصلاحية
require_once '../config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) {
    header("Location: manage_sliders.php");
    exit();
}

// التعامل مع طلب التحديث
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $title = $_POST['title'];
    $text = $_POST['text'];
    $button_text = $_POST['button_text'];
    $button_link = $_POST['button_link'];
    $slide_order = intval($_POST['slide_order']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $image_path = $_POST['current_image'];

    // التعامل مع رفع صورة جديدة
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/sliders/";
        $file_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "uploads/sliders/" . $file_name;
            if (!empty($_POST['current_image']) && file_exists("../" . $_POST['current_image'])) {
                @unlink("../" . $_POST['current_image']);
            }
        }
    }

    $stmt = $conn->prepare("UPDATE sliders SET title=?, text=?, button_text=?, button_link=?, image_path=?, slide_order=?, is_active=? WHERE id=?");
    $stmt->bind_param("sssssiii", $title, $text, $button_text, $button_link, $image_path, $slide_order, $is_active, $id);
    $stmt->execute();
    header("Location: manage_sliders.php?success=3");
    exit();
}

// جلب بيانات الشريحة الحالية
$stmt = $conn->prepare("SELECT * FROM sliders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$slide = $result->fetch_assoc();

if (!$slide) {
    header("Location: manage_sliders.php");
    exit();
}

include 'includes/header.php';
?>

<h1>تعديل الشريحة</h1>

<div class="form-container">
    <form action="edit_slider.php?id=<?php echo $id; ?>" method="POST" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        
        <div class="form-group">
            <label for="title">العنوان الرئيسي</label>
            <input type="text" name="title" id="title" value="<?php echo e($slide['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="text">النص</label>
            <textarea name="text" id="text" required><?php echo e($slide['text']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="button_text">نص الزر</label>
            <input type="text" name="button_text" id="button_text" value="<?php echo e($slide['button_text']); ?>" required>
        </div>
        <div class="form-group">
            <label for="button_link">رابط الزر</label>
            <input type="text" name="button_link" id="button_link" value="<?php echo e($slide['button_link']); ?>" required>
        </div>
        <div class="form-group">
            <label>الصورة الحالية</label>
            <img src="../<?php echo e($slide['image_path']); ?>" alt="" style="max-width: 300px; border-radius: 5px;">
            <input type="hidden" name="current_image" value="<?php echo e($slide['image_path']); ?>">
        </div>
        <div class="form-group">
            <label for="image">تغيير الصورة (اختياري)</label>
            <input type="file" name="image" id="image">
        </div>
        <div class="form-group">
            <label for="slide_order">ترتيب العرض</label>
            <input type="number" name="slide_order" id="slide_order" value="<?php echo e($slide['slide_order']); ?>" required>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" <?php echo $slide['is_active'] ? 'checked' : ''; ?>> تفعيل الشريحة</label>
        </div>

        <button type="submit">حفظ التعديلات</button>
        <a href="manage_sliders.php" style="margin-right: 15px; color: #555;">إلغاء</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>