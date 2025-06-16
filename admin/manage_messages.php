<?php
require_once 'includes/auth_check.php';
// يمكنك إضافة صلاحية خاصة لهذه الصفحة إذا أردت
// check_permission('manage_messages'); 
require_once '../config.php';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_messages.php?success=1");
    exit();
}

$messages_result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
include 'includes/header.php';
?>
<h1>رسائل "تواصل معنا"</h1>
<?php if(isset($_GET['success'])): ?><p class="success">تم حذف الرسالة بنجاح.</p><?php endif; ?>
<div class="table-container">
    <table>
        <thead><tr><th>الاسم</th><th>البريد الإلكتروني</th><th>الموضوع</th><th>تاريخ الإرسال</th><th>إجراءات</th></tr></thead>
        <tbody>
            <?php if ($messages_result && $messages_result->num_rows > 0): ?>
                <?php while($msg = $messages_result->fetch_assoc()): ?>
                <tr onclick="document.getElementById('msg-<?php echo $msg['id']; ?>').style.display='block'">
                    <td><?php echo e($msg['name']); ?></td>
                    <td><?php echo e($msg['email']); ?></td>
                    <td><?php echo e($msg['subject']); ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($msg['created_at'])); ?></td>
                    <td><a href="manage_messages.php?delete=<?php echo $msg['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">حذف</a></td>
                </tr>
                <tr class="message-body-row"><td colspan="5">
                    <div id="msg-<?php echo $msg['id']; ?>" class="message-body">
                        <p><strong>الرسالة:</strong></p>
                        <p><?php echo nl2br(e($msg['message'])); ?></p>
                    </div>
                </td></tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">لا توجد رسائل حالياً.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<style>
tr[onclick] { cursor: pointer; }
tr[onclick]:hover { background-color: #f9f9f9; }
.message-body-row td { padding: 0; border: none; }
.message-body { display: none; padding: 20px; background: #f1f5f9; border-bottom: 1px solid #ddd; }
</style>
<?php include 'includes/footer.php'; ?>