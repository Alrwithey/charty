<?php
require_once 'includes/auth_check.php';
check_permission('manage_news');
require_once '../config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) {
    header("Location: manage_news.php");
    exit();
}

// التعامل مع طلب التحديث
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $title = $_POST['title'];
    $content = $_POST['content'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $current_image = $_POST['current_image'];
    $image_path = $current_image;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/news/";
        $file_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "uploads/news/" . $file_name;
            if (!empty($current_image) && file_exists("../" . $current_image)) {
                @unlink("../" . $current_image);
            }
        }
    }

    $stmt = $conn->prepare("UPDATE news SET title=?, content=?, image=?, meta_title=?, meta_description=? WHERE id=?");
    $stmt->bind_param("sssssi", $title, $content, $image_path, $meta_title, $meta_description, $id);
    $stmt->execute();
    header("Location: manage_news.php?success=3");
    exit();
}

// جلب بيانات الخبر الحالي
$stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

if (!$article) {
    header("Location: manage_news.php");
    exit();
}

include 'includes/header.php';
?>

<!-- تضمين مكتبة TinyMCE -->
<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY_HERE/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea#content',
    directionality: 'rtl',
    language: 'ar',
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
    height: 600,
  });
</script>

<h1>تعديل الخبر: <?php echo e($article['title']); ?></h1>

<div class="form-container">
    <form action="edit_news.php?id=<?php echo $id; ?>" method="POST" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        
        <div class="form-group">
            <label for="title">عنوان الخبر</label>
            <input type="text" name="title" id="title" value="<?php echo e($article['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="content">محتوى الخبر</label>
            <textarea name="content" id="content" rows="15"><?php echo e($article['content']); ?></textarea>
        </div>

        <div class="form-group">
            <label>الصورة الحالية</label>
            <br>
            <img src="../<?php echo e($article['image']); ?>" alt="" style="max-width: 200px; border-radius: 5px;">
            <input type="hidden" name="current_image" value="<?php echo e($article['image']); ?>">
        </div>
        <div class="form-group">
            <label for="image">تغيير الصورة (اختياري)</label>
            <input type="file" name="image" id="image">
        </div>

        <hr>
        <h2>إعدادات SEO (اختياري)</h2>
        <div class="form-group">
            <label for="meta_title">عنوان SEO (سيظهر في تبويب المتصفح ونتائج جوجل)</label>
            <input type="text" name="meta_title" id="meta_title" value="<?php echo e($article['meta_title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="meta_description">وصف Meta (وصف قصير يظهر في نتائج جوجل)</label>
            <textarea name="meta_description" id="meta_description" rows="3"><?php echo e($article['meta_description'] ?? ''); ?></textarea>
        </div>

        <button type="submit">حفظ التعديلات</button>
        <a href="manage_news.php" style="margin-right: 15px; color: #555;">إلغاء</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>