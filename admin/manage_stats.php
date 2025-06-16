<?php
require_once 'includes/auth_check.php';
check_permission('manage_stats'); // التحقق من الصلاحية
require_once '../config.php';

// التعامل مع الطلبات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    if (isset($_POST['add_stat'])) {
        $stmt = $conn->prepare("INSERT INTO stats (title, value, stat_order) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $_POST['title'], $_POST['value'], $_POST['stat_order']);
        $stmt->execute();
    }

    if (isset($_POST['update_stat'])) {
        $stmt = $conn->prepare("UPDATE stats SET title=?, value=?, stat_order=? WHERE id=?");
        $stmt->bind_param("siii", $_POST['title'], $_POST['value'], $_POST['stat_order'], $_POST['id']);
        $stmt->execute();
    }
    header("Location: manage_stats.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM stats WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_stats.php");
    exit();
}

$stats_result = $conn->query("SELECT * FROM stats ORDER BY stat_order ASC");

include 'includes/header.php';
?>

<h1>إدارة الإحصائيات</h1>

<div class="form-container">
    <h2>إضافة إحصائية جديدة</h2>
    <form action="manage_stats.php" method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group">
            <label>العنوان (مثال: عدد المشاريع المنفذة)</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label>القيمة (الرقم)</label>
            <input type="number" name="value" required>
        </div>
        <div class="form-group">
            <label>ترتيب العرض</label>
            <input type="number" name="stat_order" value="0" required>
        </div>
        <button type="submit" name="add_stat">إضافة إحصائية</button>
    </form>
</div>

<div class="table-container">
    <h2>الإحصائيات الحالية</h2>
    <table>
        <thead>
            <tr>
                <th>العنوان</th>
                <th>القيمة</th>
                <th>الترتيب</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php while($stat = $stats_result->fetch_assoc()): ?>
            <tr>
                <form action="manage_stats.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
                    <input type="hidden" name="id" value="<?php echo $stat['id']; ?>">
                    <td><input type="text" name="title" value="<?php echo e($stat['title']); ?>"></td>
                    <td><input type="number" name="value" value="<?php echo e($stat['value']); ?>"></td>
                    <td><input type="number" name="stat_order" value="<?php echo e($stat['stat_order']); ?>" style="width: 70px;"></td>
                    <td>
                        <button type="submit" name="update_stat" class="btn-update">تحديث</button>
                        <a href="manage_stats.php?delete=<?php echo $stat['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>