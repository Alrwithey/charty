<?php
require_once 'config.php';
$settings = get_all_settings();
$message_sent = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // حفظ في قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    $stmt->execute();
    
    // إرسال بريد إلكتروني
    $to = e($settings['contact_email'] ?? '');
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: ". $email . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    mail($to, "رسالة جديدة من موقع الجمعية: " . $subject, $message, $headers);
    
    $message_sent = true;
}

$page_title = 'تواصل معنا - ' . ($settings['site_name'] ?? '');
include 'partials/header.php';
?>
<main class="main-content-area">
    <!-- ... (تصميم صفحة تواصل معنا هنا) ... -->
    <?php if($message_sent): ?>
        <p class="success">تم إرسال رسالتك بنجاح. شكراً لتواصلك معنا.</p>
    <?php else: ?>
        <form action="contact.php" method="POST">
            <!-- حقول النموذج: الاسم، البريد، الموضوع، الرسالة -->
        </form>
    <?php endif; ?>
</main>
<?php include 'partials/footer.php'; ?>