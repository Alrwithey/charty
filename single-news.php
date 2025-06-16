<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: news.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: news.php");
    exit();
}

$article = $result->fetch_assoc();
$settings = get_all_settings();

// استخدام بيانات SEO الخاصة بالخبر، أو استخدام بيانات افتراضية
$page_title = !empty($article['meta_title']) ? e($article['meta_title']) : e($article['title']) . ' - ' . ($settings['site_name'] ?? '');
$meta_description = !empty($article['meta_description']) ? e($article['meta_description']) : e(mb_substr(strip_tags($article['content']), 0, 160));

include 'partials/header.php';
?>
<main class="main-content-area">
    <div class="container">
        <div class="full-page-content" data-aos="fade-up">
            <h1><?php echo e($article['title']); ?></h1>
            <div class="article-meta">
                <span><i class="fas fa-calendar-alt"></i> <?php echo date('d F, Y', strtotime($article['created_at'])); ?></span>
            </div>
            <?php if (!empty($article['image'])): ?>
                <img src="<?php echo BASE_URL; ?>/<?php echo e($article['image']); ?>" alt="<?php echo e($article['title']); ?>" class="article-image">
            <?php endif; ?>
            <div class="content-body">
                <?php echo nl2br(e($article['content'])); ?>
            </div>
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