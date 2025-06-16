<?php
require_once 'includes/auth_check.php';
check_permission('manage_services'); // التحقق من الصلاحية
require_once '../config.php';

// التعامل مع الطلبات (إضافة, تحديث, حذف)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    if (isset($_POST['add_service'])) {
        $stmt = $conn->prepare("INSERT INTO services (title, description, icon_class, button_text, service_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $_POST['title'], $_POST['description'], $_POST['icon_class'], $_POST['button_text'], $_POST['service_order']);
        $stmt->execute();
    }

    if (isset($_POST['update_service'])) {
        $stmt = $conn->prepare("UPDATE services SET title=?, description=?, icon_class=?, button_text=?, service_order=? WHERE id=?");
        $stmt->bind_param("ssssii", $_POST['title'], $_POST['description'], $_POST['icon_class'], $_POST['button_text'], $_POST['service_order'], $_POST['id']);
        $stmt->execute();
    }
    header("Location: manage_services.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_services.php");
    exit();
}

$services_result = $conn->query("SELECT * FROM services ORDER BY service_order ASC");

include 'includes/header.php';
?>

<h1>إدارة الخدمات</h1>

<div class="form-container">
    <h2>إضافة خدمة جديدة</h2>
    <form action="manage_services.php" method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group">
            <label>عنوان الخدمة</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label>الوصف</label>
            <textarea name="description" required></textarea>
        </div>
        <div class="form-group">
            <label>أيقونة الخدمة (مثال: fa-solid fa-glasses)</label>
            <input type="text" name="icon_class" required>
        </div>
        <div class="form-group">
            <label>نص الزر</label>
            <input type="text" name="button_text" required>
        </div>
        <div class="form-group">
            <label>ترتيب العرض</label>
            <input type="number" name="service_order" value="0" required>
        </div>
        <button type="submit" name="add_service">إضافة الخدمة</button>
    </form>
</div>

<div class="table-container">
    <h2>الخدمات الحالية</h2>
    <form action="manage_services.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <table>
            <thead>
                <tr>
                    <th>العنوان</th>
                    <th>الوصف</th>
                    <th>الأيقونة</th>
                    <th>نص الزر</th>
                    <th>الترتيب</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while($service = $services_result->fetch_assoc()): ?>
                <tr>
                    <td><input type="text" name="title" value="<?php echo e($service['title']); ?>"></td>
                    <td><textarea name="description"><?php echo e($service['description']); ?></textarea></td>
                    <td><input type="text" name="icon_class" value="<?php echo e($service['icon_class']); ?>"></td>
                    <td><input type="text" name="button_text" value="<?php echo e($service['button_text']); ?>"></td>
                    <td><input type="number" name="service_order" value="<?php echo e($service['service_order']); ?>" style="width: 70px;"></td>
                    <td>
                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                        <button type="submit" name="update_service" class="btn-update">تحديث</button>
                        <a href="manage_services.php?delete=<?php echo $service['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </form>
</div>

<?php include 'includes/footer.php'; ?>