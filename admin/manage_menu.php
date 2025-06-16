<?php
require_once 'includes/auth_check.php';
check_permission('manag_menu'); // التحقق من الصلاحية
require_once '../config.php';

// التعامل مع الطلبات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    // إضافة عنصر جديد
    if (isset($_POST['add_item'])) {
        $stmt = $conn->prepare("INSERT INTO menu_items (title, link, parent_id, menu_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $_POST['title'], $_POST['link'], $_POST['parent_id'], $_POST['menu_order']);
        $stmt->execute();
    }
    
    // تحديث العناصر الحالية
    if (isset($_POST['update_items'])) {
        $items = $_POST['items'];
        $stmt = $conn->prepare("UPDATE menu_items SET title=?, link=?, parent_id=?, menu_order=? WHERE id=?");
        foreach ($items as $id => $item) {
            $stmt->bind_param("ssiii", $item['title'], $item['link'], $item['parent_id'], $item['menu_order'], $id);
            $stmt->execute();
        }
    }
    header("Location: manage_menu.php?success=1");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_menu.php?success=2");
    exit();
}

// جلب كل عناصر القائمة
$menu_items_result = $conn->query("SELECT * FROM menu_items ORDER BY parent_id, menu_order ASC");
$main_menu_items = [];
while($item = $menu_items_result->fetch_assoc()){
    if($item['parent_id'] == 0){
        $main_menu_items[] = $item;
    }
}

include 'includes/header.php';
?>

<h1>إدارة القائمة الرئيسية</h1>
<?php if(isset($_GET['success'])): ?><p class="success">تم حفظ التغييرات بنجاح.</p><?php endif; ?>

<!-- نموذج تحديث العناصر الحالية -->
<div class="form-container">
    <h2>القائمة الحالية</h2>
    <form action="manage_menu.php" method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <table>
            <thead>
                <tr>
                    <th>النص الظاهر</th>
                    <th>الرابط</th>
                    <th>العنصر الأب</th>
                    <th>الترتيب</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $menu_items_result->data_seek(0); // إعادة المؤشر
            while($item = $menu_items_result->fetch_assoc()): 
            ?>
                <tr>
                    <td><input type="text" name="items[<?php echo $item['id']; ?>][title]" value="<?php echo e($item['title']); ?>"></td>
                    <td><input type="text" name="items[<?php echo $item['id']; ?>][link]" value="<?php echo e($item['link']); ?>" dir="ltr" style="text-align:left;"></td>
                    <td>
                        <select name="items[<?php echo $item['id']; ?>][parent_id]">
                            <option value="0">-- عنصر رئيسي --</option>
                            <?php foreach($main_menu_items as $main_item): 
                                if($main_item['id'] == $item['id']) continue; // لا يمكن أن يكون العنصر أب لنفسه
                            ?>
                                <option value="<?php echo $main_item['id']; ?>" <?php if($item['parent_id'] == $main_item['id']) echo 'selected'; ?>>
                                    <?php echo e($main_item['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="number" name="items[<?php echo $item['id']; ?>][menu_order]" value="<?php echo e($item['menu_order']); ?>" style="width: 80px;"></td>
                    <td><a href="manage_menu.php?delete=<?php echo $item['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد؟')">حذف</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <br>
        <button type="submit" name="update_items">حفظ كل التغييرات</button>
    </form>
</div>


<!-- نموذج إضافة عنصر جديد -->
<div class="form-container">
    <h2>إضافة عنصر جديد للقائمة</h2>
    <form action="manage_menu.php" method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <div class="form-group">
            <label>النص الظاهر</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label>الرابط</label>
            <input type="text" name="link" placeholder="مثال: news.php أو index.php#services" required>
        </div>
        <div class="form-group">
            <label>العنصر الأب (للقوائم المنسدلة)</label>
            <select name="parent_id">
                <option value="0">-- اجعله عنصراً رئيسياً --</option>
                 <?php foreach($main_menu_items as $main_item): ?>
                    <option value="<?php echo $main_item['id']; ?>"><?php echo e($main_item['title']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>الترتيب</label>
            <input type="number" name="menu_order" value="0" required>
        </div>
        <button type="submit" name="add_item">إضافة للقائمة</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>