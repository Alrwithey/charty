<?php
require_once 'includes/auth_check.php';
// check_permission('manage_requests'); 
require_once '../config.php';

// التعامل مع طلبات تغيير الحالة، الحذف، أو حفظ الإجراء
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_action'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die('CSRF token validation failed.'); }
    $id = intval($_POST['request_id']);
    $action_text = $_POST['action_taken'];

    $stmt = $conn->prepare("UPDATE service_requests SET action_taken = ?, status = 'in_progress' WHERE id = ?");
    $stmt->bind_param("si", $action_text, $id);
    $stmt->execute();
    header("Location: manage_requests.php?success=1");
    exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    if (!isset($_GET['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) { die('CSRF token validation failed.'); }
    $id = intval($_GET['id']);
    
    if ($_GET['action'] == 'complete') {
        $stmt = $conn->prepare("UPDATE service_requests SET status = 'completed' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    
    if ($_GET['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM service_requests WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: manage_requests.php?success=1");
    exit();
}

$requests_result = $conn->query("SELECT * FROM service_requests ORDER BY created_at DESC");

include 'includes/header.php';
?>

<h1>نظام تتبع طلبات الخدمة</h1>

<div class="page-actions">
    <a href="export_requests.php" class="btn-add-new" style="background-color: #16a085; text-decoration: none;"><i class="fas fa-file-excel"></i> تصدير إلى Excel</a>
</div>

<?php if(isset($_GET['success'])): ?><p class="success">تم تنفيذ الإجراء بنجاح.</p><?php endif; ?>

<div class="table-container">
    <h2>قائمة الطلبات المستلمة</h2>
    <div style="overflow-x: auto;">
        <table class="messages-table">
            <thead>
                <tr>
                    <th>الحالة</th>
                    <th>مقدم الطلب</th>
                    <th>الخدمة المطلوبة</th>
                    <th>تاريخ الطلب</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($requests_result && $requests_result->num_rows > 0): ?>
                    <?php while($req = $requests_result->fetch_assoc()): 
                        
                        $status_class = 'status-new';
                        $status_text = 'جديد';
                        
                        $created_date = new DateTime($req['created_at']);
                        $now = new DateTime();
                        $interval = $now->diff($created_date);
                        $days_passed = $interval->days;

                        if ($req['status'] == 'completed') {
                            $status_class = 'status-completed';
                            $status_text = 'منتهي';
                        } elseif ($req['status'] == 'in_progress') {
                            $status_class = 'status-in-progress';
                            $status_text = 'تحت الدراسة';
                        } elseif ($days_passed >= 5) {
                            $status_class = 'status-late';
                            $status_text = 'متأخر';
                        }
                    ?>
                    <tr class="message-row <?php echo $status_class; ?>" onclick="toggleMessage(event, <?php echo $req['id']; ?>)">
                        <td><span class="status-indicator"><?php echo $status_text; ?></span></td>
                        <td><?php echo e($req['full_name']); ?></td>
                        <td><?php echo e($req['service_type']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($req['created_at'])); ?></td>
                        <td class="actions-cell">
                            <?php if ($req['status'] != 'completed'): ?>
                                <a href="manage_requests.php?action=complete&id=<?php echo $req['id']; ?>&csrf_token=<?php echo e($csrf_token); ?>" class="btn-action btn-complete" title="تحديد كمنتهي" onclick="event.stopPropagation(); return confirm('هل أنت متأكد من إنهاء هذا الطلب؟')"><i class="fas fa-check"></i></a>
                            <?php endif; ?>
                            <a href="#" class="btn-action btn-view" title="عرض التفاصيل"><i class="fas fa-eye"></i></a>
                            <a href="manage_requests.php?action=delete&id=<?php echo $req['id']; ?>&csrf_token=<?php echo e($csrf_token); ?>" class="btn-action btn-delete" title="حذف" onclick="event.stopPropagation(); return confirm('هل أنت متأكد من حذف هذا الطلب؟')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="message-body-row" id="message-body-<?php echo $req['id']; ?>">
                        <td colspan="7">
                            <div class="message-content request-details">
                                <div class="detail-grid">
                                    <div class="detail-pair"><strong>الجنس:</strong><span><?php echo e($req['gender']); ?></span></div>
                                    <div class="detail-pair"><strong>الجنسية:</strong><span><?php echo e($req['nationality']); ?></span></div>
                                    <div class="detail-pair"><strong>تاريخ الميلاد:</strong><span><?php echo e($req['birth_date']); ?> (العمر: <?php echo e($req['age']); ?> سنة)</span></div>
                                    <div class="detail-pair"><strong>الحالة الوظيفية:</strong><span><?php echo e($req['is_employed']); ?></span></div>
                                    <div class="detail-pair"><strong>مقر السكن:</strong><span><?php echo e($req['residence_city']); ?></span></div>
                                    <div class="detail-pair"><strong>الراتب:</strong><span><?php echo $req['salary'] ? e($req['salary']) . ' ريال' : 'لم يحدد'; ?></span></div>
                                </div>
                                <hr>
                                <p><strong>وصف الطلب:</strong></p>
                                <blockquote><?php echo nl2br(e($req['request_description'])); ?></blockquote>
                                <hr>
                                <div class="attachments">
                                    <strong>المرفقات:</strong>
                                    <a href="<?php echo BASE_URL . '/' . e($req['identity_file_path']); ?>" target="_blank" class="btn-download"><i class="fas fa-id-card"></i> عرض/تحميل الهوية</a>
                                    <?php if(!empty($req['reports_file_path'])): ?>
                                    <a href="<?php echo BASE_URL . '/' . e($req['reports_file_path']); ?>" target="_blank" class="btn-download"><i class="fas fa-file-medical"></i> عرض/تحميل التقارير</a>
                                    <?php endif; ?>
                                </div>
                                <hr>
                                <div class="action-form">
                                    <form action="manage_requests.php" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
                                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                        <label for="action_taken_<?php echo $req['id']; ?>"><strong>الإجراء المتخذ من قبل الفريق:</strong></label>
                                        <textarea name="action_taken" id="action_taken_<?php echo $req['id']; ?>"><?php echo e($req['action_taken']); ?></textarea>
                                        <button type="submit" name="save_action" class="btn-update">حفظ الإجراء</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center;">لا توجد طلبات حالياً.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleMessage(event, id) {
    if (event) {
        if (event.target.closest('.btn-action')) { return; }
        event.preventDefault();
    }
    
    const messageBodyRow = document.getElementById('message-body-' + id);
    if (messageBodyRow) {
        const isVisible = messageBodyRow.style.display === 'table-row';
        document.querySelectorAll('.message-body-row').forEach(row => {
            if (row.id !== 'message-body-' + id) { row.style.display = 'none'; }
        });
        messageBodyRow.style.display = isVisible ? 'none' : 'table-row';
    }
}
</script>

<?php include 'includes/footer.php'; ?>