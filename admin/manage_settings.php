<?php
require_once 'includes/auth_check.php';
check_permission('manage_settings');
require_once '../config.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $settings_to_update = [
        'site_name', 'contact_phone', 'contact_email', 'contact_address', 'main_color', 'accent_color', 'twitter_url', 
        'facebook_url', 'instagram_url', 'google_map_embed', 'font_family_headings', 'font_family_body', 
        'font_size_h1', 'font_size_h2', 'font_size_h3', 'font_size_body',
        'board_term', 'board_expiry_date', 'license_number'
    ];
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    foreach ($settings_to_update as $key) {
        if (isset($_POST[$key])) {
            $value = $_POST[$key];
            $stmt->bind_param("ss", $value, $key);
            $stmt->execute();
        }
    }
    $stmt->close();
    
    function handle_file_upload($file_input_name, $setting_key, $file_prefix) {
        global $conn, $error_message;
        if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            $file_info = pathinfo($_FILES[$file_input_name]["name"]);
            $file_name = $file_prefix . time() . "." . strtolower($file_info['extension']);
            $target_file = $target_dir . $file_name;
            $allowed_types = ['jpg', 'png', 'jpeg', 'gif', 'svg', 'pdf'];
            if(in_array(strtolower($file_info['extension']), $allowed_types)) {
                if (move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $target_file)) {
                    $file_path_for_db = "uploads/" . $file_name;
                    $stmt_file = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                    $stmt_file->bind_param("ss", $file_path_for_db, $setting_key);
                    $stmt_file->execute();
                    $stmt_file->close();
                }
            } else { $error_message .= "الملف المرفوع لـ " . $file_input_name . " غير مدعوم. "; }
        }
    }

    handle_file_upload('site_logo', 'site_logo', 'logo');
    handle_file_upload('site_logo_white', 'site_logo_white', 'logo-white');
    handle_file_upload('license_image', 'license_image', 'license');
    handle_file_upload('board_congratulations_file', 'board_congratulations_file', 'board_file_');

    if(empty($error_message)){ $success_message = "تم حفظ الإعدادات بنجاح!"; }
}

$settings = get_all_settings();
include 'includes/header.php';
?>

<h1>الإعدادات العامة</h1>
<?php if ($success_message): ?><p class="success"><?php echo $success_message; ?></p><?php endif; ?>
<?php if ($error_message): ?><p class="error"><?php echo $error_message; ?></p><?php endif; ?>

<div class="form-container">
    <form method="POST" action="manage_settings.php" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        
        <h2>الإعدادات الأساسية</h2>
        <div class="form-group"><label for="site_name">اسم الجمعية</label><input type="text" name="site_name" id="site_name" value="<?php echo e($settings['site_name'] ?? ''); ?>"></div>
        <div class="form-group"><label>الشعار الأساسي (للخلفيات الفاتحة)</label><br><img src="../<?php echo e($settings['site_logo'] ?? ''); ?>" alt="الشعار الحالي" style="max-width: 200px; background: #f1f1f1; padding: 10px; border-radius: 8px;"></div>
        <div class="form-group"><label for="site_logo">تغيير الشعار الأساسي</label><input type="file" name="site_logo" id="site_logo"></div>
        <div class="form-group"><label>الشعار الأبيض (للخلفيات الداكنة)</label><br><img src="../<?php echo e($settings['site_logo_white'] ?? ''); ?>" alt="الشعار الأبيض" style="max-width: 200px; background: #333; padding: 10px; border-radius: 8px;"></div>
        <div class="form-group"><label for="site_logo_white">تغيير الشعار الأبيض</label><input type="file" name="site_logo_white" id="site_logo_white"></div>
        <hr>
        
        <h2>معلومات التواصل</h2>
        <div class="form-group"><label for="contact_phone">رقم التواصل</label><input type="text" name="contact_phone" id="contact_phone" value="<?php echo e($settings['contact_phone'] ?? ''); ?>"></div>
        <div class="form-group"><label for="contact_email">البريد الإلكتروني</label><input type="email" name="contact_email" id="contact_email" value="<?php echo e($settings['contact_email'] ?? ''); ?>"></div>
        <div class="form-group"><label for="contact_address">العنوان</label><input type="text" name="contact_address" id="contact_address" value="<?php echo e($settings['contact_address'] ?? ''); ?>"></div>
        <div class="form-group"><label for="google_map_embed">كود تضمين خريطة جوجل</label><textarea name="google_map_embed" id="google_map_embed" rows="4" dir="ltr" style="text-align:left;"><?php echo e($settings['google_map_embed'] ?? ''); ?></textarea></div>
        <hr>

        <h2>روابط التواصل الاجتماعي</h2>
        <div class="form-group"><label for="twitter_url">رابط تويتر (X)</label><input type="text" name="twitter_url" id="twitter_url" dir="ltr" style="text-align:left;" value="<?php echo e($settings['twitter_url'] ?? ''); ?>"></div>
        <div class="form-group"><label for="facebook_url">رابط فيسبوك</label><input type="text" name="facebook_url" id="facebook_url" dir="ltr" style="text-align:left;" value="<?php echo e($settings['facebook_url'] ?? ''); ?>"></div>
        <div class="form-group"><label for="instagram_url">رابط انستغرام</label><input type="text" name="instagram_url" id="instagram_url" dir="ltr" style="text-align:left;" value="<?php echo e($settings['instagram_url'] ?? ''); ?>"></div>
        <hr>
        
        <h2>إعدادات مجلس الإدارة والترخيص</h2>
        <div class="form-group"><label for="board_term">مدة دورة المجلس (مثال: 4 سنوات)</label><input type="text" name="board_term" id="board_term" value="<?php echo e($settings['board_term'] ?? ''); ?>"></div>
        <div class="form-group"><label for="board_expiry_date">تاريخ انتهاء الدورة الحالية</label><input type="date" name="board_expiry_date" id="board_expiry_date" value="<?php echo e($settings['board_expiry_date'] ?? ''); ?>"></div>
        <div class="form-group"><label for="board_congratulations_file">ملف خطاب التهنئة (PDF)</label><input type="file" name="board_congratulations_file" id="board_congratulations_file" accept=".pdf">
            <?php if(!empty($settings['board_congratulations_file'])): ?>
                <p style="margin-top: 5px;">الملف الحالي: <a href="../<?php echo e($settings['board_congratulations_file']); ?>" target="_blank">عرض الملف</a></p>
            <?php endif; ?>
        </div>
        <div class="form-group"><label for="license_number">رقم الترخيص</label><input type="text" name="license_number" id="license_number" value="<?php echo e($settings['license_number'] ?? ''); ?>"></div>
        <div class="form-group"><label>صورة الترخيص الحالية</label><br><img src="../<?php echo e($settings['license_image'] ?? ''); ?>" alt="صورة الترخيص" style="max-width: 300px; border: 1px solid #ddd; padding: 5px;"></div>
        <div class="form-group"><label for="license_image">تغيير صورة الترخيص</label><input type="file" name="license_image" id="license_image"></div>
        <hr>

        <h2>ألوان وخطوط الموقع</h2>
        <div class="form-group"><label for="main_color">اللون الأساسي</label><input type="color" name="main_color" id="main_color" value="<?php echo e($settings['main_color'] ?? '#1a535c'); ?>"></div>
        <div class="form-group"><label for="accent_color">اللون الثانوي</label><input type="color" name="accent_color" id="accent_color" value="<?php echo e($settings['accent_color'] ?? '#f7b538'); ?>"></div>
        <div class="form-group"><label for="font_family_headings">نوع خط العناوين</label><select name="font_family_headings" id="font_family_headings" class="admin-form-select"><option value="'Tajawal', sans-serif" <?php if(isset($settings['font_family_headings']) && $settings['font_family_headings'] == "'Tajawal', sans-serif") echo 'selected'; ?>>Tajawal</option><option value="'Cairo', sans-serif" <?php if(isset($settings['font_family_headings']) && $settings['font_family_headings'] == "'Cairo', sans-serif") echo 'selected'; ?>>Cairo</option></select></div>
        <div class="form-group"><label for="font_family_body">نوع خط النصوص</label><select name="font_family_body" id="font_family_body" class="admin-form-select"><option value="'Tajawal', sans-serif" <?php if(isset($settings['font_family_body']) && $settings['font_family_body'] == "'Tajawal', sans-serif") echo 'selected'; ?>>Tajawal</option><option value="'Cairo', sans-serif" <?php if(isset($settings['font_family_body']) && $settings['font_family_body'] == "'Cairo', sans-serif") echo 'selected'; ?>>Cairo</option></select></div>
        <h3>أحجام الخطوط</h3>
        <div class="form-group"><label for="font_size_h1">حجم H1</label><input type="text" name="font_size_h1" id="font_size_h1" value="<?php echo e($settings['font_size_h1'] ?? '3rem'); ?>"></div>
        <div class="form-group"><label for="font_size_h2">حجم H2</label><input type="text" name="font_size_h2" id="font_size_h2" value="<?php echo e($settings['font_size_h2'] ?? '2.5rem'); ?>"></div>
        <div class="form-group"><label for="font_size_h3">حجم H3</label><input type="text" name="font_size_h3" id="font_size_h3" value="<?php echo e($settings['font_size_h3'] ?? '1.8rem'); ?>"></div>
        <div class="form-group"><label for="font_size_body">حجم النص الأساسي</label><input type="text" name="font_size_body" id="font_size_body" value="<?php echo e($settings['font_size_body'] ?? '16px'); ?>"></div>
        
        <button type="submit">حفظ التغييرات</button>
    </form>
</div>
<?php
include 'includes/footer.php';
?>