<?php
require_once 'config.php';
$settings = get_all_settings();
$message_status = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['full_name', 'gender', 'national_id', 'nationality', 'birth_date', 'phone_number', 'is_employed', 'residence_city', 'service_type', 'request_description'];
    foreach($required_fields as $field){
        if(empty($_POST[$field])){
            $error_message = 'يرجى تعبئة جميع الحقول التي تحمل علامة (*).';
            break;
        }
    }
    if (empty($_FILES['identity_file']['name'])) {
        $error_message = 'يرجى إرفاق صورة الهوية.';
    }

    if(empty($error_message)){
        function upload_file($file_input, $sub_folder) {
            if (isset($file_input) && $file_input['error'] == 0) {
                // التأكد من أن المسار الأساسي موجود
                $base_dir = __DIR__ . '/uploads/';
                if (!is_dir($base_dir)) { mkdir($base_dir, 0755, true); }
                
                // المسار الكامل للمجلد الفرعي
                $target_dir = $base_dir . $sub_folder . '/';
                if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
                
                $file_name = time() . '_' . preg_replace('/[^A-Za-z0-9.\-]/', '_', basename($file_input["name"]));
                $target_file = $target_dir . $file_name;

                if (move_uploaded_file($file_input["tmp_name"], $target_file)) {
                    // إرجاع المسار النسبي من المجلد الجذري للموقع
                    return "uploads/" . $sub_folder . "/" . $file_name;
                }
            }
            return null;
        }

        $identity_path = upload_file($_FILES['identity_file'], 'identities');
        $reports_path = upload_file($_FILES['reports_file'], 'reports');

        if($identity_path){
            $birth_date = $_POST['birth_date'];
            $dob = new DateTime($birth_date);
            $now = new DateTime();
            $age = $now->diff($dob)->y;
            
            $stmt = $conn->prepare("INSERT INTO service_requests (full_name, gender, national_id, nationality, birth_date, age, phone_number, is_employed, residence_city, salary, service_type, request_description, identity_file_path, reports_file_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $salary = !empty($_POST['salary']) ? intval($_POST['salary']) : NULL;
            $stmt->bind_param("sssssisississs", $_POST['full_name'], $_POST['gender'], $_POST['national_id'], $_POST['nationality'], $birth_date, $age, $_POST['phone_number'], $_POST['is_employed'], $_POST['residence_city'], $salary, $_POST['service_type'], $_POST['request_description'], $identity_path, $reports_path);
            
            if($stmt->execute()){
                $message_status = 'success';
            } else {
                $error_message = 'حدث خطأ فني أثناء حفظ الطلب. يرجى المحاولة مرة أخرى.';
            }
        } else {
            $error_message = 'حدث خطأ أثناء رفع ملف الهوية. يرجى التأكد من أن الملف صالح وأن المجلدات لديها صلاحيات الكتابة.';
        }
    }
}

$page_title = 'طلب خدمة - ' . ($settings['site_name'] ?? '');
include 'partials/header.php';
?>

<main class="main-content-area">
    <div class="page-header">
        <div class="container"><h1>طلب خدمة</h1></div>
    </div>
    
    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
        <div class="full-page-content">
            <?php if ($message_status === 'success'): ?>
                <div class="form-success-new">
                    <div class="success-icon"><i class="fas fa-check"></i></div>
                    <h2>تم استلام طلبك بنجاح</h2>
                    <div class="success-divider"></div>
                    <p>شكرًا لك. سيقوم الفريق المختص بمراجعة طلبك والتواصل معك في أقرب فرصة. رقم طلبك هو: <strong><?php echo $conn->insert_id; ?></strong></p>
                    <a href="<?php echo BASE_URL; ?>/index.php" class="btn">العودة للرئيسية</a>
                </div>
            <?php else: ?>
                <h2>نموذج طلب خدمة</h2>
                <p>يرجى تعبئة البيانات التالية بدقة ليتمكن فريقنا من دراسة طلبك وتقديم المساعدة اللازمة.</p>
                <?php if ($error_message): ?><div class="form-error"><?php echo $error_message; ?></div><?php endif; ?>
                
                <form action="request-service.php" method="POST" class="contact-form" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group"><label>الاسم الكامل <span class="required">*</span></label><input type="text" name="full_name" required></div>
                        <div class="form-group"><label>الجنس <span class="required">*</span></label><select name="gender" required><option value="">-- اختر --</option><option value="ذكر">ذكر</option><option value="أنثى">أنثى</option></select></div>
                        <div class="form-group"><label>رقم الهوية / الإقامة <span class="required">*</span></label><input type="text" name="national_id" required></div>
                        <div class="form-group"><label>الجنسية <span class="required">*</span></label>
                            <select name="nationality" required>
                                <option value="السعودية">السعودية</option>
                                <option value="مصرية">مصرية</option>
                                <option value="سورية">سورية</option>
                                <option value="أردنية">أردنية</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                        <div class="form-group"><label>تاريخ الميلاد <span class="required">*</span></label><input type="date" name="birth_date" id="birthDate" required></div>
                        <div class="form-group"><label>العمر</label><input type="text" id="age" name="age_display" readonly style="background-color: #eee; cursor: not-allowed;"></div>
                        <div class="form-group"><label>رقم الجوال <span class="required">*</span></label><input type="tel" name="phone_number" required></div>
                        <div class="form-group"><label>هل أنت موظف؟ <span class="required">*</span></label><select name="is_employed" required><option value="">-- اختر --</option><option value="نعم">نعم</option><option value="لا">لا</option></select></div>
                        <div class="form-group"><label>مقر السكن (المدينة) <span class="required">*</span></label><input type="text" name="residence_city" required></div>
                        <div class="form-group"><label>الراتب الشهري (اختياري)</label><input type="number" name="salary" placeholder="اتركه فارغاً إذا كنت غير موظف"></div>
                        <div class="form-group"><label>الخدمة المطلوبة <span class="required">*</span></label>
                            <select name="service_type" required>
                                <option value="">-- اختر الخدمة --</option>
                                <option value="عملية جراحية">عملية جراحية</option>
                                <option value="فحص نظر">فحص نظر</option>
                                <option value="نظارة طبية">نظارة طبية</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                        <div class="form-group full-width"><label>وصف مختصر للطلب <span class="required">*</span></label><textarea name="request_description" rows="5" required></textarea></div>
                        <div class="form-group"><label>إرفاق الهوية <span class="required">*</span></label><input type="file" name="identity_file" accept="image/*,.pdf" required></div>
                        <div class="form-group"><label>إرفاق تقارير (اختياري)</label><input type="file" name="reports_file" accept="image/*,.pdf"></div>
                    </div>
                    <button type="submit" class="btn">إرسال الطلب</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
document.getElementById('birthDate').addEventListener('change', function() {
    const birthDate = new Date(this.value);
    const ageInput = document.getElementById('age');
    if (this.value && !isNaN(birthDate)) {
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        ageInput.value = age >= 0 ? age : '';
    } else {
        ageInput.value = '';
    }
});
</script>

<?php include 'partials/footer.php'; ?>
</body>
</html>