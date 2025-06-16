<?php
// تفعيل عرض الأخطاء للمساعدة في التشخيص
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// استخدام __DIR__ لضمان المسار الصحيح
require_once __DIR__ . '/../config.php';

// إذا كان المستخدم مسجلاً دخوله بالفعل، قم بتوجيهه إلى لوحة التحكم
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // !! استخدام طريقة التحقق الآمنة والصحيحة !!
            if (password_verify($password, $user['password'])) {
                // كلمة المرور صحيحة
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                
                header('Location: dashboard.php');
                exit;
            }
        }
        
        $error_message = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
    } else {
        $error_message = 'يرجى إدخال اسم المستخدم وكلمة المرور.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول - لوحة التحكم</title>
    <link rel="stylesheet" href="../css/admin_style.css?v=1.1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <form method="POST" action="index.php" novalidate>
            <h2>لوحة التحكم</h2>
            <?php if ($error_message): ?>
                <p class="error"><?php echo e($error_message); ?></p>
            <?php endif; ?>
            <div class="input-group">
                <label for="username">اسم المستخدم</label>
                <input type="text" name="username" id="username" required autocomplete="username">
            </div>
            <div class="input-group">
                <label for="password">كلمة المرور</label>
                <input type="password" name="password" id="password" required autocomplete="current-password">
            </div>
            <button type="submit">تسجيل الدخول</button>
        </form>
    </div>
</body>
</html>