<?php
require_once 'includes/auth_check.php';
check_permission('manage_tabs'); // التحقق من الصلاحية
require_once '../config.php';

// التعامل مع طلبات تحديث التبويبات (ترتيب وحالة)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_tabs'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die('CSRF token validation failed.'); }

    if (isset($_POST['tabs']) && is_array($_POST['tabs'])) {
        $tabs = $_POST['tabs'];
        $stmt = $conn->prepare("UPDATE about_tabs SET title=?, tab_order=?, is_active=? WHERE id=?");
        foreach ($tabs as $id => $tab) {
            $is_active = isset($tab['is_active']) ? 1 : 0;
            $stmt->bind_param("siii", $tab['title'], $tab['tab_order'], $is_active, $id);
            $stmt->execute();
        }
    }
    header("Location: manage_tabs.php?success=1");
    exit();
}

// التعامل مع طلبات تعديل محتوى تبويب عادي
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_tab_content'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die('CSRF token validation failed.'); }
    
    $id = intval($_POST['id']);
    $content_title = $_POST['content_title'];
    $content_text = $_POST['content_text'];
    $icon_image_path = $_POST['current_icon'];

    if (isset($_FILES['icon_image']) && $_FILES['icon_image']['error'] == 0) {
        $target_dir = "../uploads/tabs/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
        $file_name = time() . '_' . basename($_FILES["icon_image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["icon_image"]["tmp_name"], $target_file)) {
            $icon_image_path = "uploads/tabs/" . $file_name;
        }
    }

    $stmt = $conn->prepare("UPDATE about_tabs SET content_title=?, content_text=?, icon_image=? WHERE id=?");
    $stmt->bind_param("sssi", $content_title, $content_text, $icon_image_path, $id);
    $stmt->execute();
    header("Location: manage_tabs.php?success=1");
    exit();
}

// التعامل مع تعديل بيانات المدير التنفيذي
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_manager'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die('CSRF token validation failed.'); }

    $name = $_POST['name'];
    $position = $_POST['position'];
    $degree = $_POST['degree'];
    $experience = $_POST['experience'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $photo_path = $_POST['current_photo'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = "manager_" . time() . '_' . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_path = "uploads/" . $file_name;
        }
    }
    
    $stmt = $conn->prepare("UPDATE executive_manager SET name=?, position=?, photo=?, degree=?, experience=?, phone=?, email=? WHERE id=1");
    $stmt->bind_param("sssssss", $name, $position, $photo_path, $degree, $experience, $phone, $email);
    $stmt->execute();
    header("Location: manage_tabs.php?success=1");
    exit();
}

// إضافة تبويب جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_new_tab'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die('CSRF token validation failed.'); }
    
    $title = $_POST['new_title'];
    $content_title = $_POST['new_content_title'];
    $content_text = $_POST['new_content_text'];
    $order = intval($_POST['new_tab_order']);
    $icon_path = '';

    if (isset($_FILES['new_icon_image']) && $_FILES['new_icon_image']['error'] == 0) {
        $target_dir = "../uploads/tabs/";
        $file_name = time() . '_' . basename($_FILES["new_icon_image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["new_icon_image"]["tmp_name"], $target_file)) {
            $icon_path = "uploads/tabs/" . $file_name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO about_tabs (title, content_title, content_text, icon_image, tab_type, tab_order, is_active) VALUES (?, ?, ?, ?, 'normal', ?, 1)");
    $stmt->bind_param("ssssi", $title, $content_title, $content_text, $icon_path, $order);
    $stmt->execute();
    header("Location: manage_tabs.php?success=1");
    exit();
}


// جلب كل التبويبات للعرض
$tabs_result = $conn->query("SELECT * FROM about_tabs ORDER BY tab_order ASC");
$manager_data = $conn->query("SELECT * FROM executive_manager WHERE id=1")->fetch_assoc();

include 'includes/header.php';
?>

<h1>إدارة قسم التعريف بالجمعية</h1>
<?php if (isset($_GET['success'])): ?>
    <p class="success">تم حفظ التغييرات بنجاح!</p>
<?php endif; ?>

<!-- نموذج التحكم بالتبويبات -->
<div class="form-container">
    <h2>التحكم بالتبويبات (إظهار/إخفاء وترتيب)</h2>
    <form action="manage_tabs.php" method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <table>
            <thead>
                <tr>
                    <th>تفعيل</th>
                    <th>اسم التبويب</th>
                    <th>ترتيب العرض</th>
                    <th>نوع المحتوى</th>
                </tr>
            </thead>
            <tbody>
            <?php while($tab = $tabs_result->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox" name="tabs[<?php echo $tab['id']; ?>][is_active]" <?php echo $tab['is_active'] ? 'checked' : ''; ?>></td>
                    <td><input type="text" name="tabs[<?php echo $tab['id']; ?>][title]" value="<?php echo e($tab['title']); ?>"></td>
                    <td><input type="number" name="tabs[<?php echo $tab['id']; ?>][tab_order]" value="<?php echo e($tab['tab_order']); ?>"></td>
                    <td><?php echo $tab['tab_type'] === 'normal' ? 'محتوى عادي' : 'بيانات المدير'; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <br>
        <button type="submit" name="update_tabs">حفظ ترتيب وحالة التبويبات</button>
    </form>
</div>

<!-- نماذج تعديل المحتوى -->
<?php
$tabs_result->data_seek(0); // إعادة المؤشر لبداية النتائج
while($tab = $tabs_result->fetch_assoc()):
    if($tab['tab_type'] === 'normal'): ?>
    <div class="form-container">
        <h2>تعديل محتوى تبويب: <?php echo e($tab['title']); ?></h2>
        <form action="manage_tabs.php" method="POST" class="admin-form" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
            <input type="hidden" name="id" value="<?php echo $tab['id']; ?>">
            <div class="form-group">
                <label>عنوان المحتوى</label>
                <input type="text" name="content_title" value="<?php echo e($tab['content_title']); ?>">
            </div>
            <div class="form-group">
                <label>النص</label>
                <textarea name="content_text" rows="5"><?php echo e($tab['content_text']); ?></textarea>
            </div>
            <div class="form-group">
                <label>الأيقونة الحالية</label>
                <?php if (!empty($tab['icon_image'])): ?>
                    <img src="../<?php echo e($tab['icon_image']); ?>" alt="ايقونة" style="width: 80px; height: 80px; background: #eee; padding: 5px; border-radius: 8px;">
                <?php else: ?>
                    <p>لا يوجد</p>
                <?php endif; ?>
                <input type="hidden" name="current_icon" value="<?php echo e($tab['icon_image']); ?>">
            </div>
            <div class="form-group">
                <label>تغيير الأيقونة (اختياري)</label>
                <input type="file" name="icon_image">
            </div>
            <button type="submit" name="update_tab_content">حفظ محتوى (<?php echo e($tab['title']); ?>)</button>
        </form>
    </div>
    <?php elseif($tab['tab_type'] === 'manager' && $manager_data): ?>
    <div class="form-container">
        <h2>تعديل بيانات المدير التنفيذي</h2>
        <form action="manage_tabs.php" method="POST" class="admin-form" enctype="multipart/form-data">
             <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
            <div class="form-group">
                <label>الاسم</label>
                <input type="text" name="name" value="<?php echo e($manager_data['name']); ?>">
            </div>
             <div class="form-group">
                <label>المنصب</label>
                <input type="text" name="position" value="<?php echo e($manager_data['position']); ?>">
            </div>
            <div class="form-group">
                <label>الصورة الشخصية الحالية</label>
                <img src="../<?php echo e($manager_data['photo']); ?>" alt="صورة المدير" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
                <input type="hidden" name="current_photo" value="<?php echo e($manager_data['photo']); ?>">
            </div>
             <div class="form-group">
                <label>تغيير الصورة (اختياري)</label>
                <input type="file" name="photo">
            </div>
            <div class="form-group">
                <label>الدرجة العلمية</label>
                <input type="text" name="degree" value="<?php echo e($manager_data['degree']); ?>">
            </div>
            <div class="form-group">
                <label>سنوات الخبرة</label>
                <input type="text" name="experience" value="<?php echo e($manager_data['experience']); ?>">
            </div>
            <div class="form-group">
                <label>رقم الجوال</label>
                <input type="text" name="phone" value="<?php echo e($manager_data['phone']); ?>">
            </div>
             <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" value="<?php echo e($manager_data['email']); ?>">
            </div>
            <button type="submit" name="update_manager">حفظ بيانات المدير</button>
        </form>
    </div>
    <?php endif; ?>
<?php endwhile; ?>

<!-- نموذج إضافة تبويب جديد -->
<div class="form-container">
    <h2>إضافة تبويب جديد</h2>
    <form action="manage_tabs.php" method="POST" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group">
            <label>اسم التبويب (مثل: قيمنا)</label>
            <input type="text" name="new_title" required>
        </div>
        <div class="form-group">
            <label>عنوان المحتوى داخل التبويب (مثل: قيم الجمعية)</label>
            <input type="text" name="new_content_title" required>
        </div>
        <div class="form-group">
            <label>النص</label>
            <textarea name="new_content_text" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label>الأيقونة (اختياري)</label>
            <input type="file" name="new_icon_image">
        </div>
        <div class="form-group">
            <label>ترتيب العرض</label>
            <input type="number" name="new_tab_order" value="99">
        </div>
        <button type="submit" name="add_new_tab">إضافة تبويب</button>
    </form>
</div>


<?php include 'includes/footer.php'; ?>