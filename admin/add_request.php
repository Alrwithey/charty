<?php
require_once 'includes/auth_check.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $title = $_POST['title'];
    $description = $_POST['description'];
    $requester_name = $_POST['requester_name'];
    $requester_info = $_POST['requester_info'];

    $stmt = $conn->prepare("INSERT INTO requests (title, description, requester_name, requester_info) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $description, $requester_name, $requester_info);
    
    if($stmt->execute()){
        header("Location: manage_requests.php?success=3");
        exit();
    } else {
        $error_message = "حدث خطأ أثناء حفظ الطلب.";
    }
}

include 'includes/header.php';
?>

<h1>إضافة طلب جديد</h1>
<?php if (isset($error_message)): ?><p class="error"><?php echo $error_message; ?></p><?php endif; ?>

<div class="form-container">
    <form action="add_request.php" method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        
        <div class="form-group">
            <label for="title">عنوان الطلب <span class="required">*</span></label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="requester_name">اسم مقدم الطلب <span class="required">*</span></label>
            <input type="text" id="requester_name" name="requester_name" required>
        </div>

        <div class="form-group">
            <label for="requester_info">معلومات التواصل (رقم هاتف، بريد، إلخ)</label>
            <input type="text" id="requester_info" name="requester_info">
        </div>

        <div class="form-group">
            <label for="description">تفاصيل الطلب <span class="required">*</span></label>
            <textarea name="description" id="description" rows="8" required></textarea>
        </div>
        
        <button type="submit">حفظ الطلب</button>
        <a href="manage_requests.php" style="margin-right: 15px; color: #555;">إلغاء</a>
    </form>
</div>


<?php include 'includes/footer.php'; ?>