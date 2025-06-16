<?php
require_once 'includes/auth_check.php';
check_permission('manage_users');
require_once '../config.php';

// قائمة بكل الصلاحيات المتاحة في النظام
$available_permissions = [
    'manage_sliders' => 'إدارة البنر المتحرك',
    'manage_settings' => 'الإعدادات العامة',
    'manage_menu' => 'إدارة القائمة',
    'manage_tabs' => 'قسم التعريف (الرئيسية)',
    'manage_pages' => 'إدارة الصفحات',
    'manage_services' => 'إدارة الخدمات',
    'manage_stats' => 'إدارة الإحصائيات',
    'manage_news' => 'إدارة الأخبار',
    'manage_projects' => 'إدارة المشاريع',
    'manage_regulations' => 'اللوائح والسياسات',
    'manage_partners' => 'شركاء النجاح',
    'manage_users' => 'إدارة المستخدمين',
];

// التعامل مع الطلبات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    // إضافة مستخدم جديد
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user_permissions = isset($_POST['permissions']) ? json_encode($_POST['permissions']) : json_encode([]);

        $stmt = $conn->prepare("INSERT INTO admin_users (username, password, permissions) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $user_permissions);
        $stmt->execute();
        header("Location: manage_users.php?success=1");
        exit();
    }

    // تحديث مستخدم
    if (isset($_POST['update_user'])) {
        $user_id = intval($_POST['user_id']);
        $username = $_POST['username'];
        $user_permissions = isset($_POST['permissions']) ? json_encode($_POST['permissions']) : json_encode([]);

        // تحديث كلمة المرور فقط إذا تم إدخال واحدة جديدة
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admin_users SET username=?, password=?, permissions=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $password, $user_permissions, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE admin_users SET username=?, permissions=? WHERE id=?");
            $stmt->bind_param("ssi", $username, $user_permissions, $user_id);
        }
        $stmt->execute();
        header("Location: manage_users.php?success=1");
        exit();
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id != 1) { // منع حذف المستخدم الرئيسي
        $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: manage_users.php?success=2");
    exit();
}


$users_result = $conn->query("SELECT * FROM admin_users");

include 'includes/header.php';
?>
<h1>إدارة المستخدمين</h1>
<?php if(isset($_GET['success'])): ?><p class="success">تم تنفيذ العملية بنجاح.</p><?php endif; ?>

<div class="form-container">
    <h2>إضافة مستخدم جديد</h2>
    <form action="manage_users.php" method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group"><label>اسم المستخدم</label><input type="text" name="username" required></div>
        <div class="form-group"><label>كلمة المرور</label><input type="password" name="password" required></div>
        <div class="form-group">
            <label>الصلاحيات</label>
            <div class="permissions-grid">
                <?php foreach($available_permissions as $key => $label): ?>
                    <label class="permission-label"><input type="checkbox" name="permissions[]" value="<?php echo $key; ?>"> <?php echo $label; ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <button type="submit" name="add_user">إضافة مستخدم</button>
    </form>
</div>

<div class="table-container">
    <h2>المستخدمون الحاليون</h2>
    <table>
        <thead><tr><th>اسم المستخدم</th><th>الإجراءات</th></tr></thead>
        <tbody>
            <?php while($user = $users_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo e($user['username']); ?></td>
                <td>
                    <a href="#" class="btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user, JSON_UNESCAPED_UNICODE)); ?>); return false;">تعديل</a>
                    <?php if ($user['id'] != 1): ?>
                    <a href="manage_users.php?delete=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">حذف</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal for editing user -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeEditModal()">×</span>
        <h2>تعديل المستخدم</h2>
        <form action="manage_users.php" method="POST" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="form-group"><label>اسم المستخدم</label><input type="text" name="username" id="edit_username" required></div>
            <div class="form-group"><label>كلمة المرور الجديدة (اتركه فارغاً لعدم التغيير)</label><input type="password" name="password"></div>
            <div class="form-group">
                <label>الصلاحيات</label>
                <div id="edit_permissions" class="permissions-grid">
                    <?php foreach($available_permissions as $key => $label): ?>
                        <label class="permission-label"><input type="checkbox" name="permissions[]" value="<?php echo $key; ?>"> <?php echo $label; ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" name="update_user">حفظ التعديلات</button>
        </form>
    </div>
</div>

<style>
.permissions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; }
.permission-label { display: flex; align-items: center; gap: 8px; background: #f9f9f9; padding: 8px; border-radius: 4px; }
.modal { display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
.modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 8px; position: relative; }
.close-btn { color: #aaa; position: absolute; left: 20px; top: 10px; font-size: 28px; font-weight: bold; }
.close-btn:hover, .close-btn:focus { color: black; text-decoration: none; cursor: pointer; }
</style>

<script>
function openEditModal(user) {
    document.getElementById('editUserModal').style.display = 'block';
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    
    // Reset all checkboxes
    const checkboxes = document.querySelectorAll('#edit_permissions input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = false);
    
    // Check the ones for the user
    const userPermissions = JSON.parse(user.permissions || '[]');
    if(user.id == 1) { // Admin user
        checkboxes.forEach(cb => {
            cb.checked = true;
            cb.disabled = true;
        });
    } else {
        checkboxes.forEach(cb => {
            cb.disabled = false;
            if (userPermissions.includes(cb.value)) {
                cb.checked = true;
            }
        });
    }
}
function closeEditModal() {
    document.getElementById('editUserModal').style.display = 'none';
}
</script>

<?php include 'includes/footer.php'; ?>