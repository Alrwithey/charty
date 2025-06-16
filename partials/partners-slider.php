<?php
// هذا الملف يتم تضمينه داخل index.php حيث تم تعريف $conn بالفعل
$partners_result = $conn->query("SELECT * FROM partners ORDER BY partner_order ASC");

if ($partners_result && $partners_result->num_rows > 0) {
    $partners = [];
    while($row = $partners_result->fetch_assoc()) {
        $partners[] = $row;
    }
    
    // !! التعديل الجديد هنا !!
    // نقوم بمضاعفة القائمة فقط إذا كان عدد الشركاء قليلًا (أقل من 8 مثلاً)
    $partners_to_display = $partners;
    if (count($partners) > 0 && count($partners) < 8) {
        $partners_to_display = array_merge($partners, $partners);
    }
?>
    <!-- ================== Partners Section ================== -->
    <section class="partners-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>شركاء النجاح</h2>
            </div>
            <div class="partners-slider-wrapper" data-aos="fade-up">
                <div class="partners-track">
                    <?php foreach($partners_to_display as $partner): ?>
                        <div class="partner-logo-container">
                            <img src="<?php echo BASE_URL . '/' . e($partner['logo']); ?>" alt="<?php echo e($partner['name']); ?>" class="partner-logo">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php 
} // نهاية شرط التحقق من وجود شركاء
?>