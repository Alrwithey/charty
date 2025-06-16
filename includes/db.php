<?php
// ===================================================================
// هذا الملف يجب أن يبدأ بـ <?php في السطر الأول تمامًا
// ===================================================================

// بدء الجلسة (Session) لاستخدامها في لوحة التحكم وغيرها
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// بيانات الاتصال بقاعدة البيانات الخاصة بك
define('DB_HOST', 'localhost');
define('DB_USER', 'u414795990_eye');
define('DB_PASS', 'Tec@2025');
define('DB_NAME', 'u414795990_eye');

// إنشاء اتصال جديد بقاعدة البيانات
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// التحقق من نجاح الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات. الخطأ: " . $conn->connect_error);
}

// ضبط ترميز الاتصال إلى utf8mb4 لضمان دعم اللغة العربية بشكل مثالي
if (!$conn->set_charset("utf8mb4")) {
    printf("خطأ في تحميل ترميز الحروف utf8mb4: %s\n", $conn->error);
    exit();
}
?>