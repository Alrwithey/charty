<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
$csrf_token = $_SESSION['csrf_token'];
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <h3>لوحة التحكم</h3>
            <ul>
                <li><a href="dashboard.php" class="<?php if($current_page == 'dashboard.php') echo 'active'; ?>"><i class="fas fa-tachometer-alt"></i> الرئيسية</a></li>
                <!-- الرابط الجديد لنظام الطلبات -->
                <li><a href="manage_requests.php" class="<?php if(in_array($current_page, ['manage_requests.php', 'add_request.php'])) echo 'active'; ?>"><i class="fas fa-clipboard-list"></i> نظام الطلبات</a></li>
                <li><a href="manage_messages.php" class="<?php if($current_page == 'manage_messages.php') echo 'active'; ?>"><i class="fas fa-envelope-open-text"></i> رسائل التواصل</a></li>
                <hr style="border-color: rgba(255,255,255,0.1); margin: 10px 0;">
                <li><a href="manage_sliders.php" class="<?php if($current_page == 'manage_sliders.php') echo 'active'; ?>"><i class="fas fa-images"></i> البنر المتحرك</a></li>
                <li><a href="manage_menu.php" class="<?php if($current_page == 'manage_menu.php') echo 'active'; ?>"><i class="fas fa-list-alt"></i> إدارة القائمة</a></li>
                <li><a href="manage_tabs.php" class="<?php if($current_page == 'manage_tabs.php') echo 'active'; ?>"><i class="fas fa-folder-open"></i> قسم التعريف (الرئيسية)</a></li>
                <li><a href="manage_pages.php" class="<?php if(in_array($current_page, ['manage_pages.php', 'edit_page.php'])) echo 'active'; ?>"><i class="fas fa-file-alt"></i> إدارة الصفحات</a></li>
                <li><a href="manage_services.php" class="<?php if($current_page == 'manage_services.php') echo 'active'; ?>"><i class="fas fa-concierge-bell"></i> إدارة الخدمات</a></li>
                <li><a href="manage_stats.php" class="<?php if($current_page == 'manage_stats.php') echo 'active'; ?>"><i class="fas fa-chart-bar"></i> إدارة الإحصائيات</a></li>
                <li><a href="manage_news.php" class="<?php if(in_array($current_page, ['manage_news.php', 'edit_news.php'])) echo 'active'; ?>"><i class="fas fa-newspaper"></i> إدارة الأخبار</a></li>
                <li><a href="manage_projects.php" class="<?php if(in_array($current_page, ['manage_projects.php', 'edit_project.php'])) echo 'active'; ?>"><i class="fas fa-tasks"></i> إدارة المشاريع</a></li>
                <li><a href="manage_regulations.php" class="<?php if($current_page == 'manage_regulations.php') echo 'active'; ?>"><i class="fas fa-file-pdf"></i> اللوائح والسياسات</a></li>
                <li><a href="manage_partners.php" class="<?php if($current_page == 'manage_partners.php') echo 'active'; ?>"><i class="fas fa-handshake"></i> شركاء النجاح</a></li>
                <hr style="border-color: rgba(255,255,255,0.1); margin: 10px 0;">
                <li><a href="manage_users.php" class="<?php if($current_page == 'manage_users.php') echo 'active'; ?>"><i class="fas fa-users-cog"></i> إدارة المستخدمين</a></li>
                <li><a href="manage_settings.php" class="<?php if($current_page == 'manage_settings.php') echo 'active'; ?>"><i class="fas fa-cog"></i> الإعدادات العامة</a></li>
            </ul>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
        </aside>
        <main class="main-content">