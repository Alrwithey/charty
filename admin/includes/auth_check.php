<?php
require_once __DIR__ . '/../../includes/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

/**
 * دالة للتحقق من الصلاحية.
 * يتم استدعاؤها في بداية كل صفحة إدارية محمية.
 * @param string $required_permission اسم الصلاحية المطلوبة.
 */
function check_permission($required_permission) {
    global $conn;

    $user_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT permissions FROM admin_users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $permissions = json_decode($user['permissions'] ?? '[]', true);

    // المستخدم الرئيسي (admin id=1) أو الذي لديه صلاحية 'all' يمكنه الوصول لكل شيء
    if ($user_id == 1 || (is_array($permissions) && in_array('all', $permissions))) {
        return true;
    }

    // التحقق إذا كان المستخدم يملك الصلاحية المطلوبة
    if (!is_array($permissions) || !in_array($required_permission, $permissions)) {
        // !! التعديل الجديد هنا !!
        // الآن سنعرض رسالة الخطأ داخل تصميم لوحة التحكم

        // أولاً، نوقف أي إخراج سابق للصفحة التي حاولت الدخول إليها
        ob_clean();
        
        // تضمين هيدر لوحة التحكم
        include __DIR__ . '/header.php';
        
        // عرض رسالة الخطأ المنسقة
        echo '<div class="form-container" style="text-align: center;">';
        echo '<h1 style="color: #e74c3c;">وصول مرفوض</h1>';
        echo '<p style="font-size: 1.2rem;">عفواً، ليس لديك الصلاحية الكافية للوصول إلى هذه الصفحة.</p>';
        echo '<a href="dashboard.php" class="btn-add-new" style="background-color:#3498db; display: inline-block; margin-top: 20px;">العودة إلى لوحة التحكم</a>';
        echo '</div>';
        
        // تضمين فوتر لوحة التحكم
        include __DIR__ . '/footer.php';
        
        // إيقاف تنفيذ بقية كود الصفحة الأصلية
        exit();
    }
}
?>