<?php
// اختر كلمات مرور جديدة هنا
$password_for_admin = 'Admin@2025'; 
$password_for_asd = 'User_Pass_123'; 

$hash_for_admin = password_hash($password_for_admin, PASSWORD_DEFAULT);
$hash_for_asd = password_hash($password_for_asd, PASSWORD_DEFAULT);

echo '<h2>تحديث كلمة مرور المستخدم admin</h2>';
echo '<p>كلمة المرور الجديدة هي: <strong>' . $password_for_admin . '</strong></p>';
echo '<p>الهاش الذي يجب نسخه إلى قاعدة البيانات هو:</p>';
echo '<textarea rows="3" cols="80" onclick="this.select()">' . $hash_for_admin . '</textarea>';
echo '<hr>';

echo '<h2>تحديث كلمة مرور المستخدم asd</h2>';
echo '<p>كلمة المرور الجديدة هي: <strong>' . $password_for_asd . '</strong></p>';
echo '<p>الهاش الذي يجب نسخه إلى قاعدة البيانات هو:</p>';
echo '<textarea rows="3" cols="80" onclick="this.select()">' . $hash_for_asd . '</textarea>';
?>