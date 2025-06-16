<?php
// تضمين ملفات الوصول لقاعدة البيانات والتحقق من الدخول
require_once 'includes/auth_check.php';
require_once '../config.php';

// اسم الملف الذي سيتم تحميله
$filename = "طلبات_الخدمة_" . date('Y-m-d_H-i-s') . ".csv";

// إعداد الهيدر لتحميل الملف كـ CSV
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// إنشاء مؤشر إلى مخرجات الملف
$output = fopen('php://output', 'w');

// إضافة BOM لضمان ظهور اللغة العربية بشكل صحيح في Excel
fputs($output, "\xEF\xBB\xBF");

// تحديد الفاصل كـ (;) بدلًا من (,)
$delimiter = ';';

// كتابة عنوان الأعمدة
$columns = [
    'رقم الطلب', 
    'الاسم الكامل', 
    'الجنس', 
    'رقم الهوية/الإقامة', 
    'الجنسية', 
    'تاريخ الميلاد', 
    'العمر', 
    'رقم الجوال', 
    'الحالة الوظيفية', 
    'مقر السكن', 
    'الراتب', 
    'الخدمة المطلوبة', 
    'وصف الطلب', 
    'الحالة', 
    'الإجراء المتخذ', 
    'تاريخ الطلب'
];
fputcsv($output, $columns, $delimiter);

// جلب البيانات من قاعدة البيانات
$requests_result = $conn->query("SELECT * FROM service_requests ORDER BY created_at DESC");

if ($requests_result && $requests_result->num_rows > 0) {
    while($row = $requests_result->fetch_assoc()) {
        $lineData = [
            $row['id'],
            $row['full_name'],
            $row['gender'],
            $row['national_id'],
            $row['nationality'],
            $row['birth_date'],
            $row['age'],
            $row['phone_number'],
            $row['is_employed'],
            $row['residence_city'],
            $row['salary'],
            $row['service_type'],
            $row['request_description'],
            $row['status'],
            $row['action_taken'],
            $row['created_at']
        ];
        fputcsv($output, $lineData, $delimiter);
    }
}

fclose($output);
exit();
?>
