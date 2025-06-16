<?php
// الخطوة الأولى والأهم: التأكد من أن المستخدم مسجل دخوله
require_once 'includes/auth_check.php';

// تضمين ملف الإعدادات الرئيسي
require_once '../config.php';

// جلب إحصائيات سريعة من قاعدة البيانات لعرضها في لوحة التحكم
$services_count = $conn->query("SELECT COUNT(*) as count FROM services")->fetch_assoc()['count'];
$stats_count = $conn->query("SELECT COUNT(*) as count FROM stats")->fetch_assoc()['count'];
$news_count = $conn->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count'];
$projects_count = $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'];
$regulations_count = $conn->query("SELECT COUNT(*) as count FROM regulations")->fetch_assoc()['count'];
$partners_count = $conn->query("SELECT COUNT(*) as count FROM partners")->fetch_assoc()['count'];

// تضمين رأس الصفحة (الهيدر) الخاص بلوحة التحكم
include 'includes/header.php';
?>

<!-- رسالة الترحيب -->
<h1>أهلاً بك، <?php echo e($_SESSION['admin_username']); ?>!</h1>
<p>هذه هي لوحة التحكم الرئيسية لموقع الجمعية. يمكنك من هنا إدارة جميع أقسام الموقع.</p>

<!-- قسم البطاقات السريعة -->
<div class="dashboard-widgets">

    <div class="widget">
        <div class="widget-icon" style="background-color: #3498db;"><i class="fas fa-tasks"></i></div>
        <div class="widget-content">
            <div class="widget-value"><?php echo $projects_count; ?></div>
            <div class="widget-title">مشروع</div>
        </div>
        <a href="manage_projects.php" class="widget-footer">إدارة المشاريع <i class="fas fa-arrow-circle-left"></i></a>
    </div>

    <div class="widget">
        <div class="widget-icon" style="background-color: #2ecc71;"><i class="fas fa-newspaper"></i></div>
        <div class="widget-content">
            <div class="widget-value"><?php echo $news_count; ?></div>
            <div class="widget-title">خبر</div>
        </div>
        <a href="manage_news.php" class="widget-footer">إدارة الأخبار <i class="fas fa-arrow-circle-left"></i></a>
    </div>

    <div class="widget">
        <div class="widget-icon" style="background-color: #e74c3c;"><i class="fas fa-concierge-bell"></i></div>
        <div class="widget-content">
            <div class="widget-value"><?php echo $services_count; ?></div>
            <div class="widget-title">خدمة</div>
        </div>
        <a href="manage_services.php" class="widget-footer">إدارة الخدمات <i class="fas fa-arrow-circle-left"></i></a>
    </div>

    <div class="widget">
        <div class="widget-icon" style="background-color: #f1c40f;"><i class="fas fa-file-pdf"></i></div>
        <div class="widget-content">
            <div class="widget-value"><?php echo $regulations_count; ?></div>
            <div class="widget-title">لائحة وسياسة</div>
        </div>
        <a href="manage_regulations.php" class="widget-footer">إدارة اللوائح <i class="fas fa-arrow-circle-left"></i></a>
    </div>
    
    <div class="widget">
        <div class="widget-icon" style="background-color: #9b59b6;"><i class="fas fa-handshake"></i></div>
        <div class="widget-content">
            <div class="widget-value"><?php echo $partners_count; ?></div>
            <div class="widget-title">شريك نجاح</div>
        </div>
        <a href="manage_partners.php" class="widget-footer">إدارة الشركاء <i class="fas fa-arrow-circle-left"></i></a>
    </div>

    <div class="widget">
        <div class="widget-icon" style="background-color: #e67e22;"><i class="fas fa-chart-bar"></i></div>
        <div class="widget-content">
            <div class="widget-value"><?php echo $stats_count; ?></div>
            <div class="widget-title">إحصائية</div>
        </div>
        <a href="manage_stats.php" class="widget-footer">إدارة الإحصائيات <i class="fas fa-arrow-circle-left"></i></a>
    </div>

</div>

<!-- روابط سريعة لأقسام الإعدادات -->
<div class="quick-links">
    <a href="manage_settings.php"><i class="fas fa-cog"></i> الإعدادات العامة</a>
    <a href="manage_tabs.php"><i class="fas fa-folder-open"></i> قسم التعريف بالجمعية</a>
    <a href="manage_pages.php"><i class="fas fa-file-alt"></i> الصفحات التعريفية</a>
</div>


<?php
// تضمين تذييل الصفحة (الفوتر) الخاص بلوحة التحكم
include 'includes/footer.php';
?>