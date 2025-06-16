<?php
require_once 'includes/auth_check.php';
check_permission('manage_sliders'); // التحقق من الصلاحية
require_once '../config.php';

// التعامل مع الطلبات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die('CSRF token validation failed.'); }

    if (isset($_POST['add_slide'])) {
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/sliders/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            $file_name = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = "uploads/sliders/" . $file_name;
            }
        }
        $stmt = $conn->prepare("INSERT INTO sliders (title, text, button_text, button_link, image_path, slide_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $stmt->bind_param("sssssii", $_POST['title'], $_POST['text'], $_POST['button_text'], $_POST['button_link'], $image_path, $_POST['slide_order'], $is_active);
        $stmt->execute();
    }
    header("Location: manage_sliders.php?success=1");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // يمكنك إضافة كود لحذف الصورة من السيرفر هنا
    $stmt = $conn->prepare("DELETE FROM sliders WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_sliders.php?success=2");
    exit();
}

$sliders_result = $conn->query("SELECT * FROM sliders ORDER BY slide_order ASC");

include 'includes/header.php';
?>

<h1>إدارة البنر المتحرك (السلايدر)</h1>
<?php if(isset($_GET['success']) && $_GET['success'] == 1): ?><p class="success">تم إضافة الشريحة بنجاح.</p><?php endif; ?>
<?php if(isset($_GET['success']) && $_GET['success'] == 2): ?><p class="success">تم حذف الشريحة بنجاح.</p><?php endif; ?>
<?php if(isset($_GET['success']) && $_GET['success'] == 3): ?><p class="success">تم تعديل الشريحة بنجاح.</p><?php endif; ?>


<div class="form-container">
    <h2>إضافة شريحة جديدة</h2>
    <form action="manage_sliders.php" method="POST" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group"><label>العنوان الرئيسي</label><input type="text" name="title" required></div>
        <div class="form-group"><label>النص</label><textarea name="text" required></textarea></div>
        <div class="form-group"><label>نص الزر</label><input type="text" name="button_text" required></div>
        <div class="form-group"><label>رابط الزر</label><input type="text" name="button_link" required></div>
        <div class="form-group"><label>الصورة (يفضل مقاس 1920x1080)</label><input type="file" name="image" required></div>
        <div class="form-group"><label>ترتيب العرض</label><input type="number" name="slide_order" value="0" required></div>
        <div class="form-group"><label><input type="checkbox" name="is_active" value="1" checked> تفعيل الشريحة</label></div>
        <button type="submit" name="add_slide">إضافة شريحة</button>
    </form>
</div>

<div class="table-container">
    <h2>الشرائح الحالية</h2>
    <table>
        <thead><tr><th>الصورة</th><th>العنوان</th><th>الحالة</th><th>إجراءات</th></tr></thead>
        <tbody>
            <?php if ($sliders_result && $sliders_result->num_rows > 0): ?>
                <?php while($slide = $sliders_result->fetch_assoc()): ?>
                <tr>
                    <td><img src="../<?php echo e($slide['image_path']); ?>" alt="" style="width: 150px; height: 70px; object-fit: cover; border-radius: 5px;"></td>
                    <td><?php echo e($slide['title']); ?></td>
                    <td><?php echo $slide['is_active'] ? '<span class="status-yes">مفعل</span>' : '<span class="status-no">معطل</span>'; ?></td>
                    <td>
                        <!-- !! زر التعديل الجديد هنا !! -->
                        <a href="edit_slider.php?id=<?php echo $slide['id']; ?>" class="btn-edit">تعديل</a>
                        <a href="manage_sliders.php?delete=<?php echo $slide['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">حذف</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                 <tr><td colspan="4" style="text-align: center;">لا توجد شرائح حالياً.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>