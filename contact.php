<?php
require_once 'config.php';
$settings = get_all_settings();
$message_status = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // التحقق من الحقول الإجبارية
    $required_fields = ['name', 'email', 'phone', 'subject', 'message'];
    foreach($required_fields as $field){
        if(empty($_POST[$field])){
            $error_message = 'يرجى تعبئة جميع الحقول التي تحمل علامة (*).';
            break;
        }
    }
    if (empty($error_message) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error_message = 'الرجاء إدخال بريد إلكتروني صحيح.';
    }

    if(empty($error_message)){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        
        if($stmt->execute()){
            // ... (كود إرسال البريد كما هو) ...
            $message_status = 'success';
        } else {
            $error_message = 'حدث خطأ أثناء حفظ الرسالة. يرجى المحاولة مرة أخرى.';
        }
    }
}

$page_title = 'تواصل معنا - ' . ($settings['site_name'] ?? '');
include 'partials/header.php';
?>

<main class="main-content-area">
    <div class="page-header">
        <div class="container"><h1>تواصل معنا</h1></div>
    </div>
    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
        <div class="contact-page-wrapper">
            <div class="contact-form-container" data-aos="fade-left">
                <?php if ($message_status === 'success'): ?>
                    <div class="form-success">
                        <i class="fas fa-check-circle"></i>
                        <h3>تم إرسال رسالتك بنجاح</h3>
                        <p>شكراً لتواصلك معنا، سنقوم بالرد في أقرب وقت ممكن.</p>
                        <a href="<?php echo BASE_URL; ?>/index.php" class="btn">العودة للرئيسية</a>
                    </div>
                <?php else: ?>
                    <h2>أرسل لنا رسالة</h2>
                    <p>نسعد باستقبال استفساراتكم ومقترحاتكم.</p>
                    <?php if ($error_message): ?><div class="form-error"><?php echo $error_message; ?></div><?php endif; ?>
                    <form action="contact.php" method="POST" class="contact-form">
                        <div class="form-grid">
                            <div class="form-group"><label for="name">الاسم الكامل <span class="required">*</span></label><input type="text" id="name" name="name" required></div>
                            <div class="form-group"><label for="email">البريد الإلكتروني <span class="required">*</span></label><input type="email" id="email" name="email" required></div>
                            <div class="form-group"><label for="phone">رقم الجوال <span class="required">*</span></label><input type="tel" id="phone" name="phone" required></div>
                            <div class="form-group"><label for="subject">الموضوع <span class="required">*</span></label><input type="text" id="subject" name="subject" required></div>
                            <div class="form-group full-width"><label for="message">رسالتك <span class="required">*</span></label><textarea name="message" id="message" rows="6" required></textarea></div>
                        </div>
                        <button type="submit" class="btn">إرسال الرسالة</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="contact-info-container" data-aos="fade-right">
                <h3>معلومات التواصل المباشر</h3>
                <p>يمكنك التواصل معنا مباشرة عبر القنوات التالية.</p>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> <span><?php echo e($settings['contact_address'] ?? ''); ?></span></li>
                    <li><i class="fas fa-phone"></i> <a href="tel:<?php echo e($settings['contact_phone'] ?? ''); ?>"><?php echo e($settings['contact_phone'] ?? ''); ?></a></li>
                    <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo e($settings['contact_email'] ?? ''); ?>"><?php echo e($settings['contact_email'] ?? ''); ?></a></li>
                </ul>
                <div class="social-icons-contact">
                    <a href="<?php echo e($settings['twitter_url'] ?? '#'); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="<?php echo e($settings['facebook_url'] ?? '#'); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo e($settings['instagram_url'] ?? '#'); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'partials/footer.php'; ?>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>/js/script.js?v=<?php echo time(); ?>"></script>
<script>AOS.init({ duration: 800, once: true, offset: 50 });</script>
</body>
</html>