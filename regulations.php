<?php
require_once 'config.php';
$settings = get_all_settings();

$search_term = '';
if (isset($_GET['search'])) {
    $search_term = $conn->real_escape_string($_GET['search']);
    $regulations_result = $conn->query("SELECT * FROM regulations WHERE title LIKE '%$search_term%' ORDER BY regulation_order ASC, id DESC");
} else {
    $regulations_result = $conn->query("SELECT * FROM regulations ORDER BY regulation_order ASC, id DESC");
}

$page_title = 'اللوائح والسياسات - ' . ($settings['site_name'] ?? '');
$meta_description = 'اطلع على اللوائح والسياسات المعتمدة في الجمعية.';

include 'partials/header.php';
?>

<main class="main-content-area">
    <div class="page-header" style="background-image: url('<?php echo BASE_URL; ?>/images/regulations-header-bg.jpg');">
        <div class="container">
            <h1>اللوائح والسياسات</h1>
        </div>
    </div>
    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
        <div class="search-bar-container" data-aos="fade-up">
            <form action="regulations.php" method="GET">
                <input type="text" name="search" placeholder="ابحث بالاسم أو كلمات مفتاحية..." value="<?php echo e($search_term); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="regulations-list">
            <?php if ($regulations_result && $regulations_result->num_rows > 0): ?>
                <?php while($regulation = $regulations_result->fetch_assoc()): ?>
                    <div class="regulation-item" data-aos="fade-up">
                        <span class="regulation-title"><i class="fas fa-file-alt icon-before-title"></i><?php echo e($regulation['title']); ?></span>
                        <a href="<?php echo BASE_URL . '/' . e($regulation['file_path']); ?>" target="_blank" class="btn-download">
                            <i class="fas fa-download"></i>
                            <span>تحميل</span>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; grid-column: 1 / -1; font-size: 1.2rem; padding: 40px;">لا توجد نتائج مطابقة لبحثك.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
include 'partials/footer.php';
?>
<!-- استدعاء ملفات JS في نهاية الصفحة -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>/js/script.js?v=<?php echo time(); ?>"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 50 });
</script>
</body>
</html>