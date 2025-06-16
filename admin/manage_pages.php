<?php
require_once 'includes/auth_check.php';
check_permission('manage_pages'); // التحقق من الصلاحية
require_once '../config.php';

// التعامل مع طلب الحذف
if (isset($_GET['delete'])) {
    if (!isset($_GET['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        die('CSRF token validation failed.');
    }
    $id = intval($_GET['delete']);
    // لا نسمح بحذف أول 4 صفحات أساسية لضمان عدم كسر الموقع
    if ($id > 4) {
        $stmt = $conn->prepare("DELETE FROM pages WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: manage_pages.php?success=2");
    exit();
}

$pages_result = $conn->query("SELECT * FROM pages ORDER BY page_order ASC");

include 'includes/header.php';
?>
<h1>إدارة الصفحات التعريفية</h1>

<div class="form-container" style="text-align: left; padding: 20px;">
    <a href="edit_page.php" class="btn-add-new"><i class="fas fa-plus"></i> إضافة صفحة جديدة</a>
</div>

<?php if(isset($_GET['success']) && $_GET['success'] == 1): ?><p class="success">تم حفظ الصفحة بنجاح.</p><?php endif; ?>
<?php if(isset($_GET['success']) && $_GET['success'] == 2): ?><p class="success">تم حذف الصفحة بنجاح.</p><?php endif; ?>

<div class="table-container">
    <h2>الصفحات الحالية</h2>
    <table>
        <thead>
            <tr>
                <th>عنوان الصفحة</th>
                <th>الرابط الدائم (Slug)</th>
                <th>يظهر في القائمة؟</th>
                <th>الترتيب</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($pages_result && $pages_result->num_rows > 0): ?>
                <?php while($page = $pages_result->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo e($page['title']); ?></strong></td>
                    <td>/page.php?slug=<?php echo e($page['slug']); ?></td>
                    <td><?php echo $page['is_in_menu'] ? '<span class="status-yes">نعم</span>' : '<span class="status-no">لا</span>'; ?></td>
                    <td><?php echo e($page['page_order']); ?></td>
                    <td>
                        <a href="edit_page.php?id=<?php echo $page['id']; ?>" class="btn-edit">تعديل</a>
                        <?php if ($page['id'] > 4): // السماح بحذف الصفحات المضافة فقط ?>
                            <a href="manage_pages.php?delete=<?php echo $page['id']; ?>&csrf_token=<?php echo e($csrf_token); ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذه الصفحة؟ لا يمكن التراجع عن هذا الإجراء.')">حذف</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">لا توجد صفحات حالياً.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>