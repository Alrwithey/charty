<?php
// هذا الملف لا يتصل بقاعدة البيانات مباشرة، بل يفترض أنه تم تضمينه بعد ملف db.php

/**
 * دالة لتنظيف المخرجات قبل عرضها في HTML للحماية من هجمات XSS.
 * @param string|null $string السلسلة النصية المراد تنظيفها.
 * @return string السلسلة النصية بعد التنظيف.
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * دالة لجلب جميع الإعدادات من قاعدة البيانات وتخزينها في مصفوفة.
 * هذا أكثر كفاءة من إجراء استعلام منفصل لكل إعداد.
 * @return array مصفوفة تحتوي على جميع الإعدادات.
 */
function get_all_settings() {
    // نستخدم 'global' للوصول إلى متغير الاتصال $conn الذي تم إنشاؤه في ملف db.php
    global $conn;

    // استخدم متغيرًا ثابتًا لتخزين الإعدادات بعد جلبها مرة واحدة لتجنب الاستعلامات المتكررة
    static $settings = null;

    // إذا لم يتم جلب الإعدادات من قبل، قم بجلبها الآن
    if ($settings === null) {
        $settings = [];
        
        // التأكد من أن الاتصال موجود قبل محاولة استخدامه
        if ($conn) {
            $result = $conn->query("SELECT setting_key, setting_value FROM settings");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $settings[$row['setting_key']] = $row['setting_value'];
                }
            }
        }
    }
    return $settings;
}
?>