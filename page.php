<?php
require_once 'config.php';

// التأكد من وجود slug في الرابط، وإلا يتم التوجيه للصفحة الرئيسية
if (!isset($_GET['slug'])) {
    header("Location: index.php");
    exit();
}

$slug = $conn->real_escape_string($_GET['slug']);

// استعلام لجلب بيانات الصفحة المطلوبة
$stmt = $conn->prepare("SELECT * FROM pages WHERE slug = ? LIMIT 1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

// إذا لم يتم العثور على الصفحة، يتم التوجيه للصفحة الرئيسية
if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

// جلب بيانات الصفحة
$page = $result->fetch_assoc();

// جلب الإعدادات العامة للموقع
$settings = get_all_settings();

// تحديد عنوان الصفحة الذي سيظهر في تبويب المتصفح
$page_title = e($page['title']) . ' - ' . ($settings['site_name'] ?? '');

// تضمين ملف الهيدر
include 'partials/header.php';
?>
<main class="main-content-area">
    <div class="page-header">
        <div class="container">
            <h1><?php echo e($page['title']); ?></h1>
        </div>
    </div>
    
    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
        <div class="full-page-content" data-aos="fade-up">
            <div class="content-body">
                <?php 
                    // !! هذا هو الجزء الذي تم تعديله !!
                    // أولاً، نتحقق مما إذا كان المحتوى موجودًا وغير فارغ
                    if (!empty($page['content'])) {
                        // إذا كان موجودًا، نعرضه (نسمح بوسوم HTML)
                        echo $page['content'];
                    } else {
                        // إذا كان فارغًا، نعرض رسالة افتراضية
                        echo "<p>لم يتم إضافة محتوى لهذه الصفحة بعد.</p>";
                    }
                ?>
            </div>
        </div>
    </div>
</main>
<?php
// تضمين ملف الفوتر
include 'partials/footer.php';
?>