<?php
require_once 'includes/auth_check.php';
check_permission('manage_pages');
check_permission('edit_page'); // التحقق من الصلاحية
require_once '../config.php';
$page_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_editing = $page_id > 0;
$page_data = [
    'title' => '',
    'slug' => '',
    'content' => '',
    'is_in_menu' => 1,
    'page_order' => 0
];
$error_message = '';

if ($is_editing) {
    $stmt = $conn->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->bind_param("i", $page_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $page_data = $result->fetch_assoc();
    } else {
        header("Location: manage_pages.php");
        exit();
    }
}

// التعامل مع إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $title = $_POST['title'];
    $slug = $_POST['slug'];
    // المحتوى الآن سيأتي من TinyMCE، وهو يحتوي على HTML، لذلك لا نستخدم e()
    $content = $_POST['content']; 
    $is_in_menu = isset($_POST['is_in_menu']) ? 1 : 0;
    $page_order = intval($_POST['page_order']);

    // إنشاء slug فريد إذا لم يتم توفيره
    if (empty($slug)) {
        // تنظيف العنوان لإنشاء slug آمن
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
        $slug = trim($slug, '-');
    } else {
        // تنظيف الـ slug المدخل يدويًا
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
        $slug = trim($slug, '-');
    }
    
    if ($is_editing) {
        // تحديث صفحة موجودة
        $stmt = $conn->prepare("UPDATE pages SET title=?, slug=?, content=?, is_in_menu=?, page_order=? WHERE id=?");
        $stmt->bind_param("sssiii", $title, $slug, $content, $is_in_menu, $page_order, $page_id);
    } else {
        // إضافة صفحة جديدة
        $stmt = $conn->prepare("INSERT INTO pages (title, slug, content, is_in_menu, page_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $title, $slug, $content, $is_in_menu, $page_order);
    }

    if ($stmt->execute()) {
        header("Location: manage_pages.php?success=1");
        exit();
    } else {
        $error_message = "خطأ في الحفظ، قد يكون الرابط الدائم (Slug) مستخدمًا بالفعل. الخطأ: " . $stmt->error;
    }
}

include 'includes/header.php';
?>

<!-- ================== تضمين مكتبة TinyMCE ================== -->
<script src="https://cdn.tiny.cloud/1/cjever5mu1htwwx17vqbliz5s2qlkihlumslrq25zmsa8pkb/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea#content', // استهداف الـ textarea بالـ ID الخاص به
    directionality: 'rtl', // تحديد اتجاه الكتابة من اليمين لليسار
    language: 'ar',      // تفعيل اللغة العربية
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
    height: 600, // ارتفاع المحرر
    image_caption: true,
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    noneditable_class: 'mceNonEditable',
    toolbar_mode: 'sliding',
    contextmenu: 'link image table',
    content_style: 'body { font-family:Tajawal,Helvetica,Arial,sans-serif; font-size:16px }'
  });
</script>
<!-- ======================================================== -->


<h1><?php echo $is_editing ? 'تعديل صفحة: ' . e($page_data['title']) : 'إضافة صفحة جديدة'; ?></h1>

<?php if (isset($error_message)): ?><p class="error"><?php echo $error_message; ?></p><?php endif; ?>

<div class="form-container">
    <form action="edit_page.php<?php if($is_editing) echo '?id='.$page_id; ?>" method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        
        <div class="form-group">
            <label for="title">عنوان الصفحة</label>
            <input type="text" name="title" id="title" value="<?php echo e($page_data['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="slug">الرابط الدائم (Slug)</label>
            <input type="text" name="slug" id="slug" value="<?php echo e($page_data['slug']); ?>" required dir="ltr" style="text-align:left;">
            <p style="font-size: 0.9rem; color: #777;">مهم جداً: استخدم حروف إنجليزية وأرقام وشرطات (-) فقط. هذا الجزء سيظهر في رابط الصفحة.</p>
        </div>

        <div class="form-group">
            <label for="content">المحتوى</label>
            <!-- هذا هو حقل النص الذي سيتم تحويله إلى محرر متقدم -->
            <textarea name="content" id="content" rows="20"><?php echo e($page_data['content']); ?></textarea>
        </div>
        
        <hr style="margin: 30px 0;">

        <div class="form-group">
            <label>إعدادات الظهور</label>
            <div>
                <input type="checkbox" id="is_in_menu" name="is_in_menu" value="1" <?php echo $page_data['is_in_menu'] ? 'checked' : ''; ?>>
                <label for="is_in_menu" style="display:inline; font-weight:normal;">إظهار هذه الصفحة في القائمة المنسدلة "عن الجمعية"</label>
            </div>
        </div>
        
        <div class="form-group">
            <label for="page_order">ترتيب الظهور في القائمة</label>
            <input type="number" name="page_order" id="page_order" value="<?php echo e($page_data['page_order']); ?>" style="width: 120px;">
        </div>

        <button type="submit"><?php echo $is_editing ? 'حفظ التعديلات' : 'إنشاء الصفحة'; ?></button>
        <a href="manage_pages.php" style="margin-right: 15px; color: #555; text-decoration: none;">إلغاء</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>